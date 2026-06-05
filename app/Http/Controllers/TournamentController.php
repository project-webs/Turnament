<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Services\BracketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
    public function __construct(private BracketService $bracketService) {}

    public function index()
    {
        $tournaments = Auth::user()->tournaments()
            ->withCount('participants')
            ->latest()
            ->paginate(12);

        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'description'        => 'nullable|string|max:500',
            'type'               => 'required|in:single_elimination,round_robin',
            'third_place_match'  => 'nullable|boolean',
            'seeded'             => 'nullable|boolean',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
        ]);

        $tournament = Auth::user()->tournaments()->create([
            'name'              => $validated['name'],
            'description'       => $validated['description'] ?? null,
            'type'              => $validated['type'],
            'third_place_match' => $validated['type'] === 'single_elimination' ? $request->boolean('third_place_match') : false,
            'seeded'            => $request->boolean('seeded'),
            'status'            => 'pending',
            'start_date'        => $validated['start_date'] ?? null,
            'end_date'          => $validated['end_date'] ?? null,
        ]);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Turnamen berhasil dibuat! Tambahkan peserta sekarang.');
    }

    public function show(Tournament $tournament)
    {
        $this->authorize('view', $tournament);

        $tournament->load([
            'participants.player',
            'matches.participant1',
            'matches.participant2',
            'matches.winner',
        ]);

        // Group matches by round for bracket display
        $matchesByRound = $tournament->matches
            ->where('bracket', '!=', 'third_place')
            ->groupBy('round');

        $thirdPlaceMatch = $tournament->matches
            ->firstWhere('bracket', 'third_place');

        $totalRounds = $matchesByRound->keys()->max() ?? 0;

        $players = Auth::user()->players()->orderBy('name')->get();

        return view('tournaments.show', compact(
            'tournament', 'matchesByRound', 'thirdPlaceMatch', 'totalRounds', 'players'
        ));
    }

    public function edit(Tournament $tournament)
    {
        $this->authorize('update', $tournament);
        return view('tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        if ($tournament->status !== 'pending') {
            return back()->with('error', 'Turnamen yang sudah dimulai tidak bisa diedit.');
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'description'       => 'nullable|string|max:500',
            'type'              => 'required|in:single_elimination,round_robin',
            'third_place_match' => 'nullable|boolean',
            'seeded'            => 'nullable|boolean',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
        ]);

        $tournament->update([
            'name'              => $validated['name'],
            'description'       => $validated['description'] ?? null,
            'type'              => $validated['type'],
            'third_place_match' => $validated['type'] === 'single_elimination' ? $request->boolean('third_place_match') : false,
            'seeded'            => $request->boolean('seeded'),
            'start_date'        => $validated['start_date'] ?? null,
            'end_date'          => $validated['end_date'] ?? null,
        ]);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Turnamen berhasil diperbarui.');
    }

    public function destroy(Tournament $tournament)
    {
        $this->authorize('delete', $tournament);
        $tournament->delete();

        return redirect()->route('tournaments.index')
            ->with('success', 'Turnamen berhasil dihapus.');
    }

    public function start(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        if ($tournament->status !== 'pending') {
            return back()->with('error', 'Turnamen sudah dimulai.');
        }

        if ($tournament->participants()->count() < 2) {
            return back()->with('error', 'Minimal 2 peserta diperlukan untuk memulai turnamen.');
        }

        try {
            $playersPerGroup = $request->input('players_per_group');
            $this->bracketService->generate($tournament, $playersPerGroup);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Bracket berhasil di-generate! Turnamen telah dimulai.');
    }

    public function resetBracket(Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        $tournament->matches()->delete();
        $tournament->update(['status' => 'pending']);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Bracket berhasil di-reset.');
    }
}
