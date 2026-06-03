<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\Participant;
use Illuminate\Support\Collection;

class BracketService
{
    /**
     * Generate a Single Elimination bracket for a tournament.
     */
    public function generate(Tournament $tournament, ?int $playersPerGroup = null): void
    {
        if ($tournament->type === 'round_robin') {
            $this->generateRoundRobin($tournament, $playersPerGroup);
            return;
        }

        // Delete existing matches first
        $tournament->matches()->delete();

        $participants = $tournament->participants()->get();
        $count = $participants->count();

        if ($count < 2) {
            throw new \InvalidArgumentException('Minimal 2 peserta diperlukan.');
        }

        // Find next power of 2
        $size = (int) pow(2, ceil(log($count, 2)));
        $totalRounds = (int) log($size, 2);

        // Seed or shuffle participants
        if ($tournament->seeded) {
            $participants = $participants->sortBy('seed')->values();
        } else {
            $participants = $participants->shuffle()->values();
        }

        // Pad with nulls (BYE slots)
        $slots = $participants->toArray();
        while (count($slots) < $size) {
            $slots[] = null;
        }

        // Apply seeding order: 1 vs size, 2 vs size-1, etc. (bracket order)
        $orderedSlots = $this->buildBracketOrder($slots, $size);

        // Create Round 1 matches
        $matchMap = []; // [round][matchNumber] => TournamentMatch id
        $round1Matches = [];

        for ($i = 0; $i < $size / 2; $i++) {
            $p1 = $orderedSlots[$i * 2] ?? null;
            $p2 = $orderedSlots[$i * 2 + 1] ?? null;
            $isBye = ($p1 === null || $p2 === null);

            $match = TournamentMatch::create([
                'tournament_id'   => $tournament->id,
                'round'           => 1,
                'match_number'    => $i + 1,
                'bracket'         => 'winners',
                'participant1_id' => $p1 ? $p1['id'] : null,
                'participant2_id' => $p2 ? $p2['id'] : null,
                'status'          => $isBye ? 'finished' : 'pending',
                'is_bye'          => $isBye,
                'winner_id'       => $isBye ? ($p1 ? $p1['id'] : ($p2 ? $p2['id'] : null)) : null,
            ]);

            $matchMap[1][$i + 1] = $match->id;
            $round1Matches[] = $match;
        }

        // Create subsequent rounds
        for ($round = 2; $round <= $totalRounds; $round++) {
            $matchesInRound = (int) ($size / pow(2, $round));
            for ($i = 0; $i < $matchesInRound; $i++) {
                $label = ($round === $totalRounds) ? 'final' : 'winners';
                $match = TournamentMatch::create([
                    'tournament_id' => $tournament->id,
                    'round'         => $round,
                    'match_number'  => $i + 1,
                    'bracket'       => $label,
                    'status'        => 'pending',
                ]);
                $matchMap[$round][$i + 1] = $match->id;
            }
        }

        // Third place match
        if ($tournament->third_place_match && $totalRounds >= 2) {
            $thirdPlace = TournamentMatch::create([
                'tournament_id' => $tournament->id,
                'round'         => $totalRounds,
                'match_number'  => 2,
                'bracket'       => 'third_place',
                'status'        => 'pending',
            ]);
            $matchMap[$totalRounds][2] = $thirdPlace->id;
        }

        // Wire up next_match_id for each match (point winners to next round)
        for ($round = 1; $round < $totalRounds; $round++) {
            $matchesInRound = (int) ($size / pow(2, $round));
            for ($i = 1; $i <= $matchesInRound; $i++) {
                $nextMatchNumber = (int) ceil($i / 2);
                $nextMatchId = $matchMap[$round + 1][$nextMatchNumber] ?? null;
                $slot = ($i % 2 === 1); // odd match -> slot1, even match -> slot2

                if ($nextMatchId) {
                    TournamentMatch::where('id', $matchMap[$round][$i])->update([
                        'next_match_id'   => $nextMatchId,
                        'next_match_slot' => $slot,
                    ]);
                }
            }
        }

        // Auto-advance winners from BYE matches
        foreach ($round1Matches as $match) {
            if ($match->is_bye && $match->winner_id && $match->next_match_id) {
                $this->advanceWinner($match);
            }
        }

        // Update tournament status
        $tournament->update(['status' => 'ongoing', 'participant_count' => $count]);
    }

    private function generateRoundRobin(Tournament $tournament, ?int $playersPerGroup = null): void
    {
        $tournament->matches()->delete();
        $participants = $tournament->participants()->get();
        $realCount = $participants->count();

        if ($realCount < 2) {
            throw new \InvalidArgumentException('Minimal 2 peserta diperlukan.');
        }

        if (!$tournament->seeded) {
            $participants = $participants->shuffle()->values();
        } else {
            $participants = $participants->sortBy('seed')->values();
        }

        // Grouping Logic
        $groups = [];
        if ($playersPerGroup && $playersPerGroup > 0 && $playersPerGroup < $realCount) {
            $groupCount = (int) ceil($realCount / $playersPerGroup);
            $groupNames = range('A', 'Z');
            
            // Distribute participants to groups (Snake draft style or linear)
            // Linear distribution
            foreach ($participants as $index => $participant) {
                $groupIndex = $index % $groupCount;
                $gName = $groupNames[$groupIndex] ?? 'Group ' . ($groupIndex + 1);
                
                $participant->update(['group_name' => $gName]);
                $groups[$gName][] = $participant;
            }
        } else {
            // Single group
            $gName = 'A';
            foreach ($participants as $participant) {
                $participant->update(['group_name' => $gName]);
                $groups[$gName][] = $participant;
            }
        }

        // Generate matches per group
        foreach ($groups as $groupName => $groupParticipants) {
            $items = collect($groupParticipants)->toArray();
            $count = count($items);

            if ($count < 2) continue; // Skip if group has only 1 person

            if ($count % 2 !== 0) {
                $items[] = null; // BYE dummy
                $count++;
            }

            $totalRounds = $count - 1;
            $halfSize = $count / 2;

            for ($round = 1; $round <= $totalRounds; $round++) {
                for ($i = 0; $i < $halfSize; $i++) {
                    $p1 = $items[$i];
                    $p2 = $items[$count - 1 - $i];

                    if ($p1 !== null && $p2 !== null) {
                        TournamentMatch::create([
                            'tournament_id'   => $tournament->id,
                            'round'           => $round,
                            'match_number'    => $i + 1,
                            'bracket'         => 'round_robin_' . $groupName,
                            'participant1_id' => $p1['id'],
                            'participant2_id' => $p2['id'],
                            'status'          => 'pending',
                            'is_bye'          => false,
                        ]);
                    }
                }

                // Rotate items (keep index 0 fixed)
                $first = $items[0];
                $others = array_slice($items, 1);
                $last = array_pop($others);
                array_unshift($others, $last);
                $items = array_merge([$first], $others);
            }
        }

        $tournament->update(['status' => 'ongoing', 'participant_count' => $realCount]);
    }

    /**
     * Advance winner of a finished match to the next round.
     */
    public function advanceWinner(TournamentMatch $match): void
    {
        if ($match->tournament->type === 'round_robin') return;

        if (!$match->next_match_id || !$match->winner_id) return;

        $nextMatch = TournamentMatch::find($match->next_match_id);
        if (!$nextMatch) return;

        if ($match->next_match_slot === true) {
            $nextMatch->participant1_id = $match->winner_id;
        } else {
            $nextMatch->participant2_id = $match->winner_id;
        }

        // Check if both slots filled and one is BYE (TBD)
        $nextMatch->save();

        // If next match now has both participants, set it to pending (playable)
        if ($nextMatch->participant1_id && $nextMatch->participant2_id) {
            $nextMatch->update(['status' => 'pending']);
        }
    }

    /**
     * Build standard bracket seeding order.
     * Seeds are placed so that 1 vs last, 2 vs second-last, etc.
     */
    private function buildBracketOrder(array $slots, int $size): array
    {
        if ($size === 1) return $slots;

        $result = array_fill(0, $size, null);
        $positions = $this->generatePositions($size);

        foreach ($positions as $index => $pos) {
            $result[$pos] = $slots[$index] ?? null;
        }

        return $result;
    }

    private function generatePositions(int $size): array
    {
        if ($size === 2) return [0, 1];

        $prev = $this->generatePositions($size / 2);
        $result = [];

        foreach ($prev as $pos) {
            $result[] = $pos * 2;
            $result[] = $size - 1 - $pos * 2;
        }

        return $result;
    }

    /**
     * Check if tournament is finished (final match has a winner).
     */
    public function checkTournamentFinished(Tournament $tournament): void
    {
        if ($tournament->type === 'round_robin') {
            $unfinished = $tournament->matches()->where('status', '!=', 'finished')->exists();
            if (!$unfinished) {
                $tournament->update(['status' => 'finished']);
            }
            return;
        }

        $finalMatch = $tournament->matches()
            ->where('bracket', 'final')
            ->first();

        if ($finalMatch && $finalMatch->winner_id) {
            // Check third place too if applicable
            if ($tournament->third_place_match) {
                $thirdPlace = $tournament->matches()
                    ->where('bracket', 'third_place')
                    ->first();
                if ($thirdPlace && !$thirdPlace->winner_id) return;
            }

            $tournament->update(['status' => 'finished']);
        }
    }
}
