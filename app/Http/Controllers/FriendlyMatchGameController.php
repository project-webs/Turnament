<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendlyMatch;
use App\Models\FriendlyMatchGame;

class FriendlyMatchGameController extends Controller
{
    public function store(Request $request, FriendlyMatch $friendlyMatch)
    {
        if ($friendlyMatch->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'player_id'     => 'required|exists:players,id',
            'opponent_name' => 'required|string|max:255',
            'score_home'    => 'required|integer|min:0',
            'score_away'    => 'required|integer|min:0',
        ]);

        $friendlyMatch->games()->create($validated);

        $this->recalculateScores($friendlyMatch);

        return back()->with('success', 'Data partai berhasil ditambahkan.');
    }

    public function destroy(FriendlyMatch $friendlyMatch, FriendlyMatchGame $game)
    {
        if ($friendlyMatch->user_id !== auth()->id()) abort(403);

        $game->delete();

        $this->recalculateScores($friendlyMatch);

        return back()->with('success', 'Data partai berhasil dihapus.');
    }

    private function recalculateScores(FriendlyMatch $match)
    {
        $games = $match->games;
        $home = 0;
        $away = 0;

        foreach ($games as $game) {
            if ($game->score_home > $game->score_away) {
                $home++;
            } elseif ($game->score_away > $game->score_home) {
                $away++;
            }
        }

        $match->update([
            'total_score_home' => $home,
            'total_score_away' => $away,
        ]);
    }
}
