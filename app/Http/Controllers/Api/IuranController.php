<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        $iurans = Iuran::whereHas('player', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with('player')->latest('tanggal')->get();

        return response()->json(['data' => $iurans]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'tanggal'   => 'required|date',
            'period'    => 'required|date',
            'amount'    => 'required|integer|min:0',
            'notes'     => 'nullable|string',
        ]);

        $player = $request->user()->players()->find($validated['player_id']);
        
        if (!$player) {
            return response()->json(['message' => 'Player not found or unauthorized'], 403);
        }

        $iuran = $player->iurans()->create($validated);

        return response()->json([
            'message' => 'Data iuran berhasil ditambahkan',
            'data' => $iuran
        ], 201);
    }

    public function show(Request $request, Iuran $iuran)
    {
        if ($iuran->player->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $iuran->load('player');

        return response()->json(['data' => $iuran]);
    }

    public function update(Request $request, Iuran $iuran)
    {
        if ($iuran->player->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'player_id' => 'sometimes|required|exists:players,id',
            'tanggal'   => 'sometimes|required|date',
            'period'    => 'sometimes|required|date',
            'amount'    => 'sometimes|required|integer|min:0',
            'notes'     => 'nullable|string',
        ]);

        if (isset($validated['player_id'])) {
            $player = $request->user()->players()->find($validated['player_id']);
            if (!$player) {
                return response()->json(['message' => 'Player not found or unauthorized'], 403);
            }
        }

        $iuran->update($validated);

        return response()->json([
            'message' => 'Data iuran berhasil diperbarui',
            'data' => $iuran
        ]);
    }

    public function destroy(Request $request, Iuran $iuran)
    {
        if ($iuran->player->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $iuran->delete();

        return response()->json(['message' => 'Data iuran berhasil dihapus']);
    }
}
