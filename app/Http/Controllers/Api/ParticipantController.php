<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');
        return response()->json(['data' => $tournament->participants]);
    }

    public function show(Request $request, Tournament $tournament, Participant $participant)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');
        if ($participant->tournament_id !== $tournament->id) abort(404);
        return response()->json(['data' => $participant]);
    }

    public function update(Request $request, Tournament $tournament, Participant $participant)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');
        if ($participant->tournament_id !== $tournament->id) abort(404);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'seed' => 'nullable|integer|min:1'
        ]);

        $participant->update($validated);

        return response()->json([
            'message' => 'Peserta berhasil diupdate.',
            'data' => $participant
        ]);
    }

    public function store(Request $request, Tournament $tournament)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($tournament->status !== 'pending') {
            return response()->json(['message' => 'Peserta tidak bisa ditambah setelah turnamen dimulai.'], 422);
        }

        $request->validate([
            'player_id' => 'nullable|exists:players,id',
            'name'      => 'nullable|string|max:100',
            'seed'      => 'nullable|integer|min:1',
        ]);

        if (!$request->player_id && empty($request->name)) {
            return response()->json(['message' => 'Silakan pilih peserta yang ada atau masukkan nama peserta baru.'], 422);
        }

        $playerId = $request->player_id;
        $name = $request->name;

        if ($playerId) {
            $player = $request->user()->players()->find($playerId);
            if ($player) {
                $name = $player->name;
            } else {
                return response()->json(['message' => 'Player not found'], 404);
            }
        } else if ($name) {
            $player = $request->user()->players()->firstOrCreate(
                ['name' => $name],
                ['itr_rating' => 0]
            );
            $playerId = $player->id;
        }

        $participant = $tournament->participants()->create([
            'player_id' => $playerId,
            'name'      => $name,
            'seed'      => $request->seed,
        ]);

        return response()->json([
            'message' => 'Peserta berhasil ditambahkan.',
            'data' => $participant
        ], 201);
    }

    public function destroy(Request $request, Tournament $tournament, Participant $participant)
    {
        if ($tournament->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        if ($participant->tournament_id !== $tournament->id) {
            return response()->json(['message' => 'Participant not found in this tournament.'], 404);
        }

        if ($tournament->status !== 'pending') {
            return response()->json(['message' => 'Peserta tidak bisa dihapus setelah turnamen dimulai.'], 422);
        }

        $participant->delete();

        return response()->json(['message' => 'Peserta berhasil dihapus.']);
    }
}
