<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TournamentMatch;
use App\Services\BracketService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(private BracketService $bracketService) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'participant1_id' => 'nullable|exists:participants,id',
            'participant2_id' => 'nullable|exists:participants,id',
            'round' => 'required|integer',
            'match_number' => 'required|integer',
            'is_bye' => 'boolean'
        ]);

        $match = TournamentMatch::create($validated);
        return response()->json(['message' => 'Match created', 'data' => $match], 201);
    }

    public function destroy(Request $request, TournamentMatch $match)
    {
        if ($match->tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');
        $match->delete();
        return response()->json(['message' => 'Match deleted']);
    }

    public function index(Request $request)
    {
        $matches = TournamentMatch::whereHas('tournament', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['tournament', 'participant1', 'participant2', 'winner'])
            ->latest()
            ->paginate(15);

        return response()->json($matches);
    }

    public function show(Request $request, TournamentMatch $match)
    {
        if ($match->tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $match->load(['tournament', 'participant1', 'participant2', 'winner']);

        return response()->json(['data' => $match]);
    }

    public function update(Request $request, TournamentMatch $match)
    {
        $tournament = $match->tournament;
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($match->is_bye) {
            return response()->json(['message' => 'Match BYE tidak perlu diisi skor.'], 422);
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

        $this->bracketService->advanceWinner($match);
        $this->bracketService->checkTournamentFinished($tournament);

        return response()->json([
            'message' => 'Skor berhasil disimpan!',
            'data' => $match
        ]);
    }

    public function reset(Request $request, TournamentMatch $match)
    {
        $tournament = $match->tournament;
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($match->is_bye) {
            return response()->json(['message' => 'Match BYE tidak bisa di-reset.'], 422);
        }

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

        if ($tournament->status === 'finished') {
            $tournament->update(['status' => 'ongoing']);
        }

        return response()->json(['message' => 'Skor berhasil direset.']);
    }
}
