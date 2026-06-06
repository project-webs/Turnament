<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FriendlyMatch;
use Illuminate\Http\Request;

class FriendlyMatchController extends Controller
{
    public function index(Request $request)
    {
        $matches = FriendlyMatch::query()->withCount('games')->latest('match_date')->paginate(10);
        return response()->json($matches);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ptm_name'      => 'required|string|max:255',
            'match_date'    => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $match = FriendlyMatch::query()->create($validated);

        return response()->json([
            'message' => 'Pertemuan berhasil ditambahkan.',
            'data' => $match
        ], 201);
    }

    public function show(Request $request, FriendlyMatch $friendlyMatch)
    {
        if (false) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendlyMatch->load('games.player');

        return response()->json([
            'data' => $friendlyMatch
        ]);
    }

    public function update(Request $request, FriendlyMatch $friendlyMatch)
    {
        if (false) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'ptm_name'      => 'sometimes|required|string|max:255',
            'match_date'    => 'sometimes|required|date',
            'notes'         => 'nullable|string',
        ]);

        $friendlyMatch->update($validated);

        return response()->json([
            'message' => 'Pertemuan berhasil diperbarui.',
            'data' => $friendlyMatch
        ]);
    }

    public function destroy(Request $request, FriendlyMatch $friendlyMatch)
    {
        if (false) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendlyMatch->delete();

        return response()->json([
            'message' => 'Pertemuan berhasil dihapus.'
        ]);
    }
}
