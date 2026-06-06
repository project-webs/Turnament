<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        $query = Iuran::with('player')->whereHas('player', function($q) {
            $q->where('user_id', Auth::id());
        });

        if ($request->filled('search')) {
            $query->whereHas('player', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $iurans = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('iuran.index', compact('iurans'));
    }

    public function create()
    {
        $players = Auth::user()->players()->orderBy('name')->get();
        return view('iuran.create', compact('players'));
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

        $player = Auth::user()->players()->findOrFail($validated['player_id']);
        $player->iurans()->create($validated);

        return redirect()->route('iuran.index')->with('success', 'Data iuran berhasil ditambahkan.');
    }

    public function show(Iuran $iuran)
    {
        return redirect()->route('iuran.edit', $iuran);
    }

    public function edit(Iuran $iuran)
    {
        if ($iuran->player->user_id !== Auth::id()) abort(403);
        $players = Auth::user()->players()->orderBy('name')->get();
        return view('iuran.edit', compact('iuran', 'players'));
    }

    public function update(Request $request, Iuran $iuran)
    {
        if ($iuran->player->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'tanggal'   => 'required|date',
            'period'    => 'required|date',
            'amount'    => 'required|integer|min:0',
            'notes'     => 'nullable|string',
        ]);

        Auth::user()->players()->findOrFail($validated['player_id']);

        $iuran->update($validated);

        return redirect()->route('iuran.index')->with('success', 'Data iuran berhasil diperbarui.');
    }

    public function destroy(Iuran $iuran)
    {
        if ($iuran->player->user_id !== Auth::id()) abort(403);
        
        $iuran->delete();
        return redirect()->route('iuran.index')->with('success', 'Data iuran berhasil dihapus.');
    }
}
