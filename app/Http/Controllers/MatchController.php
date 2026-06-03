<?php

namespace App\Http\Controllers;

use App\Models\TournamentMatch;
use App\Services\BracketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function __construct(private BracketService $bracketService) {}

    public function update(Request $request, TournamentMatch $match)
    {
        $tournament = $match->tournament;
        $this->authorize('update', $tournament);

        if ($match->is_bye) {
            return back()->with('error', 'Match BYE tidak perlu diisi skor.');
        }

        $request->validate([
            'score1'     => 'required|integer|min:0|max:99',
            'score2'     => 'required|integer|min:0|max:99',
            'winner_id'  => 'required|in:' . $match->participant1_id . ',' . $match->participant2_id,
            'points_p1'  => 'nullable|array',
            'points_p1.*'=> 'nullable|integer|min:0|max:99',
            'points_p2'  => 'nullable|array',
            'points_p2.*'=> 'nullable|integer|min:0|max:99',
        ]);

        $pointHistory = null;
        if ($request->has('points_p1') && $request->has('points_p2')) {
            $pointHistory = [];
            foreach ($request->points_p1 as $index => $p1Score) {
                $p2Score = $request->points_p2[$index] ?? 0;
                // Only save if at least one player scored something
                if ($p1Score > 0 || $p2Score > 0) {
                    $pointHistory[] = [
                        'p1' => (int) $p1Score,
                        'p2' => (int) $p2Score,
                    ];
                }
            }
        }

        $match->update([
            'score1'        => $request->score1,
            'score2'        => $request->score2,
            'winner_id'     => $request->winner_id,
            'point_history' => $pointHistory,
            'status'        => 'finished',
        ]);

        // Advance winner to next round
        $this->bracketService->advanceWinner($match);

        // Check if tournament is finished
        $this->bracketService->checkTournamentFinished($tournament);

        return back()->with('success', 'Skor berhasil disimpan!');
    }

    public function reset(TournamentMatch $match)
    {
        $tournament = $match->tournament;
        $this->authorize('update', $tournament);

        if ($match->is_bye) {
            return back()->with('error', 'Match BYE tidak bisa di-reset.');
        }

        // Remove winner from next match if already advanced
        if ($match->next_match_id && $match->winner_id) {
            $nextMatch = TournamentMatch::find($match->next_match_id);
            if ($nextMatch) {
                if ($match->next_match_slot === true) {
                    $nextMatch->update([
                        'participant1_id' => null,
                        'score1' => null, 'score2' => null,
                        'winner_id' => null, 'status' => 'pending'
                    ]);
                } else {
                    $nextMatch->update([
                        'participant2_id' => null,
                        'score1' => null, 'score2' => null,
                        'winner_id' => null, 'status' => 'pending'
                    ]);
                }
            }
        }

        $match->update([
            'score1'        => null,
            'score2'        => null,
            'winner_id'     => null,
            'point_history' => null,
            'status'        => 'pending',
        ]);

        // Revert tournament to ongoing if it was finished
        if ($tournament->status === 'finished') {
            $tournament->update(['status' => 'ongoing']);
        }

        return back()->with('success', 'Skor berhasil direset.');
    }
}
