<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FriendlyMatch;
use App\Models\FriendlyMatchGame;
use Illuminate\Http\Request;

class FriendlyMatchGameController extends Controller
{
    public function store(Request $request, FriendlyMatch $friendlyMatch)
    {
        if ($friendlyMatch->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'player_id'     => 'required|exists:players,id',
            'opponent_name' => 'required|string|max:255',
            'score_home'    => 'required|integer|min:0',
            'score_away'    => 'required|integer|min:0',
        ]);

        $game = $friendlyMatch->games()->create($validated);

        $this->recalculateScores($friendlyMatch);

        return response()->json([
            'message' => 'Data partai berhasil ditambahkan.',
            'data' => $game
        ], 201);
    }

    public function destroy(Request $request, FriendlyMatch $friendlyMatch, FriendlyMatchGame $game)
    {
        if ($friendlyMatch->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($game->friendly_match_id !== $friendlyMatch->id) {
            return response()->json(['message' => 'Game not found in this match'], 404);
        }

        $game->delete();

        $this->recalculateScores($friendlyMatch);

        return response()->json([
            'message' => 'Data partai berhasil dihapus.'
        ]);
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
