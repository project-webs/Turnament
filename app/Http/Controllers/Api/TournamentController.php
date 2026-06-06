<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\BracketService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function __construct(private BracketService $bracketService) {}

    public function index(Request $request)
    {
        $tournaments = $request->user()->tournaments()
            ->withCount('participants')
            ->latest()
            ->paginate(12);

        return response()->json($tournaments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string|max:500',
            'type'               => 'required|in:single_elimination,round_robin',
            'third_place_match'  => 'nullable|boolean',
            'seeded'             => 'nullable|boolean',
        ]);

        $tournament = $request->user()->tournaments()->create([
            'name'              => $validated['name'],
            'description'       => $validated['description'] ?? null,
            'type'              => $validated['type'],
            'third_place_match' => $validated['type'] === 'single_elimination' ? $request->boolean('third_place_match') : false,
            'seeded'            => $request->boolean('seeded'),
            'status'            => 'pending',
        ]);

        return response()->json([
            'message' => 'Turnamen berhasil dibuat!',
            'data' => $tournament
        ], 201);
    }

    public function show(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $tournament->load([
            'participants.player',
            'matches.participant1',
            'matches.participant2',
            'matches.winner',
        ]);

        $tournament->append('standings');
        return response()->json(['data' => $tournament]);
    }

    public function update(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($tournament->status !== 'pending') {
            return response()->json(['message' => 'Turnamen yang sudah dimulai tidak bisa diedit.'], 422);
        }

        $validated = $request->validate([
            'name'              => 'sometimes|required|string|max:100',
            'description'       => 'nullable|string|max:500',
            'type'              => 'sometimes|required|in:single_elimination,round_robin',
            'third_place_match' => 'nullable|boolean',
            'seeded'            => 'nullable|boolean',
        ]);

        if(isset($validated['type'])) {
            $validated['third_place_match'] = $validated['type'] === 'single_elimination' ? $request->boolean('third_place_match') : false;
        }

        $tournament->update($validated);

        return response()->json([
            'message' => 'Turnamen berhasil diperbarui.',
            'data' => $tournament
        ]);
    }

    public function destroy(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $tournament->delete();

        return response()->json(['message' => 'Turnamen berhasil dihapus.']);
    }

    public function start(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($tournament->status !== 'pending') {
            return response()->json(['message' => 'Turnamen sudah dimulai.'], 422);
        }

        if ($tournament->participants()->count() < 2) {
            return response()->json(['message' => 'Minimal 2 peserta diperlukan untuk memulai turnamen.'], 422);
        }

        try {
            $playersPerGroup = $request->input('players_per_group');
            $this->bracketService->generate($tournament, $playersPerGroup);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Bracket berhasil di-generate! Turnamen telah dimulai.']);
    }

    public function resetBracket(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $tournament->matches()->delete();
        $tournament->update(['status' => 'pending']);

        return response()->json(['message' => 'Bracket berhasil di-reset.']);
    }
}
