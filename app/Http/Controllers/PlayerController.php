<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = \Illuminate\Support\Facades\Auth::user()->players();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $players = $query->orderBy('name')->paginate(15);

        $unprocessedCount = \App\Models\TournamentMatch::where('is_rating_processed', false)
            ->where('status', 'finished')
            ->whereNotNull('winner_id')
            ->whereHas('tournament', function($q) {
                $q->where('user_id', \Illuminate\Support\Facades\Auth::id());
            })
            ->count();

        return view('players.index', compact('players', 'unprocessedCount'));
    }

    public function create()
    {
        return view('players.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'division'   => 'nullable|string|max:50',
            'itr_rating' => 'nullable|integer|min:0',
        ]);

        \Illuminate\Support\Facades\Auth::user()->players()->create([
            'name'       => $validated['name'],
            'division'   => $validated['division'] ?? null,
            'itr_rating' => $validated['itr_rating'] ?? 0,
        ]);

        return redirect()->route('players.index')->with('success', 'Data peserta berhasil ditambahkan.');
    }

    public function edit(Player $player)
    {
        if ($player->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }
        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        if ($player->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'division'   => 'nullable|string|max:50',
            'itr_rating' => 'nullable|integer|min:0',
        ]);

        $player->update([
            'name'       => $validated['name'],
            'division'   => $validated['division'] ?? null,
            'itr_rating' => $validated['itr_rating'] ?? 0,
        ]);

        return redirect()->route('players.index')->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(Player $player)
    {
        if ($player->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $player->delete();
        return redirect()->route('players.index')->with('success', 'Data peserta berhasil dihapus.');
    }

    public function calculateRating()
    {
        // Get all unprocessed finished matches for the logged-in user's tournaments
        $matches = \App\Models\TournamentMatch::where('is_rating_processed', false)
            ->where('status', 'finished')
            ->whereNotNull('winner_id')
            ->whereHas('tournament', function($q) {
                $q->where('user_id', \Illuminate\Support\Facades\Auth::id());
            })
            ->with(['participant1.player', 'participant2.player'])
            ->orderBy('updated_at', 'asc') // Process oldest finished first
            ->get();

        $processedCount = 0;

        foreach ($matches as $match) {
            $p1 = $match->participant1;
            $p2 = $match->participant2;

            // Only process if both have linked players
            if (!$p1 || !$p2 || !$p1->player_id || !$p2->player_id) {
                $match->update(['is_rating_processed' => true]);
                continue;
            }

            $player1 = $p1->player;
            $player2 = $p2->player;

            $rating1 = $player1->itr_rating;
            $rating2 = $player2->itr_rating;

            $diff = abs($rating1 - $rating2);

            // USATT table
            $expectedWinPts = 0;
            $upsetWinPts = 0;

            if ($diff <= 12) { $expectedWinPts = 8; $upsetWinPts = 8; }
            elseif ($diff <= 37) { $expectedWinPts = 7; $upsetWinPts = 10; }
            elseif ($diff <= 62) { $expectedWinPts = 6; $upsetWinPts = 13; }
            elseif ($diff <= 87) { $expectedWinPts = 5; $upsetWinPts = 16; }
            elseif ($diff <= 112) { $expectedWinPts = 4; $upsetWinPts = 20; }
            elseif ($diff <= 137) { $expectedWinPts = 3; $upsetWinPts = 25; }
            elseif ($diff <= 162) { $expectedWinPts = 2; $upsetWinPts = 30; }
            elseif ($diff <= 187) { $expectedWinPts = 2; $upsetWinPts = 35; }
            elseif ($diff <= 212) { $expectedWinPts = 1; $upsetWinPts = 40; }
            elseif ($diff <= 237) { $expectedWinPts = 1; $upsetWinPts = 45; }
            else { $expectedWinPts = 0; $upsetWinPts = 50; }

            $isP1Winner = ($match->winner_id === $p1->id);
            $pointsExchanged = 0;

            if ($isP1Winner) {
                if ($rating1 >= $rating2) {
                    $pointsExchanged = $expectedWinPts;
                } else {
                    $pointsExchanged = $upsetWinPts;
                }
                $player1->itr_rating += $pointsExchanged;
                $player2->itr_rating -= $pointsExchanged;
            } else {
                if ($rating2 >= $rating1) {
                    $pointsExchanged = $expectedWinPts;
                } else {
                    $pointsExchanged = $upsetWinPts;
                }
                $player2->itr_rating += $pointsExchanged;
                $player1->itr_rating -= $pointsExchanged;
            }

            // Prevent negative rating
            if ($player1->itr_rating < 0) $player1->itr_rating = 0;
            if ($player2->itr_rating < 0) $player2->itr_rating = 0;

            $player1->save();
            $player2->save();

            $match->update(['is_rating_processed' => true]);
            $processedCount++;
        }

        return back()->with('success', "$processedCount pertandingan telah dikalkulasi untuk ITR Rating.");
    }
}
