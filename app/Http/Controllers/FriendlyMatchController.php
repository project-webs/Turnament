<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendlyMatch;

class FriendlyMatchController extends Controller
{
    public function index()
    {
        $matches = auth()->user()->friendlyMatches()->withCount('games')->latest('match_date')->paginate(10);
        return view('friendly-matches.index', compact('matches'));
    }

    public function create()
    {
        return view('friendly-matches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ptm_name'      => 'required|string|max:255',
            'match_date'    => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $match = auth()->user()->friendlyMatches()->create($validated);

        return redirect()->route('friendly-matches.show', $match)
            ->with('success', 'Pertemuan berhasil ditambahkan. Silakan tambahkan data pemain.');
    }

    public function show(FriendlyMatch $friendlyMatch)
    {
        if ($friendlyMatch->user_id !== auth()->id()) {
            abort(403);
        }

        $friendlyMatch->load('games.player');
        $players = auth()->user()->players()->orderBy('name')->get();

        return view('friendly-matches.show', compact('friendlyMatch', 'players'));
    }

    public function destroy(FriendlyMatch $friendlyMatch)
    {
        if ($friendlyMatch->user_id !== auth()->id()) {
            abort(403);
        }
        
        $friendlyMatch->delete();
        return redirect()->route('friendly-matches.index')
            ->with('success', 'Pertandingan persahabatan berhasil dihapus.');
    }
}
