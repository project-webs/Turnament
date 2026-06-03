<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    public function store(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        if ($tournament->status !== 'pending') {
            return back()->with('error', 'Peserta tidak bisa ditambah setelah turnamen dimulai.');
        }

        $request->validate([
            'player_id' => 'nullable|exists:players,id',
            'name'      => 'nullable|string|max:100', // Either player_id or name must be present
            'seed'      => 'nullable|integer|min:1',
        ]);

        if (!$request->player_id && empty($request->name)) {
            return back()->with('error', 'Silakan pilih peserta yang ada atau masukkan nama peserta baru.');
        }

        $playerId = $request->player_id;
        $name = $request->name;

        // If player_id is provided, get the name from player
        if ($playerId) {
            $player = Auth::user()->players()->find($playerId);
            if ($player) {
                $name = $player->name;
            }
        } 
        // If no player_id but name is provided, find or create player
        else if ($name) {
            $player = Auth::user()->players()->firstOrCreate(
                ['name' => $name],
                ['itr_rating' => 0]
            );
            $playerId = $player->id;
        }

        $tournament->participants()->create([
            'player_id' => $playerId,
            'name'      => $name,
            'seed'      => $request->seed,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function bulkStore(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        if ($tournament->status !== 'pending') {
            return back()->with('error', 'Peserta tidak bisa ditambah setelah turnamen dimulai.');
        }

        $request->validate([
            'names' => 'required|string',
        ]);

        $lines = explode("\n", $request->names);
        $added = 0;

        foreach ($lines as $line) {
            $name = trim($line);
            if (!empty($name)) {
                $player = Auth::user()->players()->firstOrCreate(
                    ['name' => $name],
                    ['itr_rating' => 0]
                );

                $tournament->participants()->create([
                    'player_id' => $player->id,
                    'name'      => $player->name
                ]);
                $added++;
            }
        }

        return back()->with('success', "$added peserta berhasil ditambahkan.");
    }

    public function destroy(Tournament $tournament, Participant $participant)
    {
        $this->authorize('update', $tournament);

        if ($tournament->status !== 'pending') {
            return back()->with('error', 'Peserta tidak bisa dihapus setelah turnamen dimulai.');
        }

        $participant->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
