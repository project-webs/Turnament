<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->players();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 100);
        $players = $query->orderBy('name')->paginate($perPage);

        return response()->json($players);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'gender'     => 'nullable|in:Laki-laki,Perempuan',
            'nik'        => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'division'   => 'nullable|string|max:50',
            'itr_rating' => 'nullable|integer|min:0',
        ]);

        $player = $request->user()->players()->create([
            'name'       => $validated['name'],
            'gender'     => $validated['gender'] ?? null,
            'nik'        => $validated['nik'] ?? null,
            'address'    => $validated['address'] ?? null,
            'division'   => $validated['division'] ?? null,
            'itr_rating' => $validated['itr_rating'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Data peserta berhasil ditambahkan.',
            'data' => $player
        ], 201);
    }

    public function show(Request $request, Player $player)
    {
        if ($player->user_id !== $request->user()->id) abort(403, 'Unauthorized');
        
        return response()->json(['data' => $player]);
    }

    public function update(Request $request, Player $player)
    {
        if ($player->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $validated = $request->validate([
            'name'       => 'sometimes|required|string|max:100',
            'gender'     => 'nullable|in:Laki-laki,Perempuan',
            'nik'        => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'division'   => 'nullable|string|max:50',
            'itr_rating' => 'nullable|integer|min:0',
        ]);

        $player->update($validated);

        return response()->json([
            'message' => 'Data peserta berhasil diperbarui.',
            'data' => $player
        ]);
    }

    public function destroy(Request $request, Player $player)
    {
        if ($player->user_id !== $request->user()->id) abort(403, 'Unauthorized');

        $player->delete();

        return response()->json(['message' => 'Data peserta berhasil dihapus.']);
    }
}
