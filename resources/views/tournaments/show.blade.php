@extends('layouts.app')

@section('title', $tournament->name)
@section('page-title', $tournament->name)

@section('topbar-actions')
    <span class="badge badge-{{ $tournament->status_color }}">
        <i class="fa-solid fa-circle" style="font-size:7px"></i>
        {{ $tournament->status_label }}
    </span>
    @if($tournament->status === 'pending')
        <a href="{{ route('tournaments.edit', $tournament) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
    @endif
    <form method="POST" action="{{ route('tournaments.destroy', $tournament) }}"
          onsubmit="return confirm('Yakin ingin menghapus turnamen ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
@endsection

@push('styles')
<style>
/* ── PAGE LAYOUT ──────────────────────── */
.show-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 1100px) {
    .show-layout { grid-template-columns: 1fr; }
}

/* ── TABS ──────────────────────────────── */
.tab-nav {
    display: flex;
    gap: 4px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 6px;
    margin-bottom: 20px;
}
.tab-btn {
    flex: 1;
    padding: 10px 16px;
    border: none;
    background: transparent;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all var(--transition);
    font-family: 'Inter', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.tab-btn.active {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 4px 14px var(--accent-glow);
}
.tab-btn:not(.active):hover {
    background: var(--bg-card-hover);
    color: var(--text-primary);
}

.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* ── BRACKET ───────────────────────────── */
.bracket-scroll {
    overflow-x: auto;
    padding-bottom: 16px;
}
.bracket {
    display: flex;
    gap: 0;
    min-width: fit-content;
    padding: 20px 0;
}
.bracket-round {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    min-width: 220px;
    position: relative;
}
.bracket-round-label {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 16px 16px;
}
.bracket-matches {
    display: flex;
    flex-direction: column;
    flex: 1;
    justify-content: space-around;
    gap: 0;
}
.bracket-match-wrap {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
    padding: 8px 0;
}

/* Connector lines */
.bracket-match-wrap::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    width: 28px;
    height: 1px;
    background: var(--border-light);
}
.bracket-round:last-child .bracket-match-wrap::after { display: none; }

.bracket-match {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    width: 192px;
    transition: all var(--transition);
    position: relative;
    margin: 0 14px;
    flex-shrink: 0;
}
.bracket-match:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 20px rgba(59,130,246,0.2);
}
.bracket-match.finished {
    border-color: var(--border);
}
.bracket-match.clickable {
    cursor: pointer;
}
.bracket-match.clickable:hover {
    transform: scale(1.02);
}

.match-num {
    position: absolute;
    top: 6px;
    right: 8px;
    font-size: 10px;
    color: var(--text-muted);
    font-weight: 600;
}

.match-player {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    min-height: 42px;
    gap: 8px;
}
.match-player + .match-player {
    border-top: 1px solid var(--border);
}
.match-player-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.match-player-name.tbd {
    color: var(--text-muted);
    font-weight: 400;
    font-style: italic;
}
.match-player-name.bye {
    color: var(--text-muted);
    font-weight: 400;
}
.match-player-score {
    font-size: 15px;
    font-weight: 800;
    min-width: 24px;
    text-align: center;
}
.match-player.winner .match-player-name { color: var(--green); }
.match-player.winner .match-player-score { color: var(--green); }
.match-player.loser .match-player-name { color: var(--text-muted); }
.match-player.loser .match-player-score { color: var(--text-muted); }

.match-badge {
    text-align: center;
    padding: 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    background: linear-gradient(90deg, var(--accent), var(--purple));
    color: #fff;
}

/* Final special styling */
.bracket-match.final-match {
    width: 200px;
    border: 2px solid rgba(245,158,11,0.4);
    box-shadow: 0 0 20px rgba(245,158,11,0.15);
}
.bracket-match.final-match .match-badge {
    background: linear-gradient(90deg, #f59e0b, #ef4444);
}
.bracket-match.third-place-match {
    width: 200px;
    border: 2px solid rgba(168,85,247,0.3);
}
.bracket-match.third-place-match .match-badge {
    background: linear-gradient(90deg, #a855f7, #6366f1);
}

/* ── PARTICIPANTS PANEL ─────────────── */
.participants-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.participant-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    transition: all var(--transition);
}
.participant-item:hover { border-color: var(--border-light); }
.participant-item.draggable {
    cursor: grab;
    user-select: none;
}
.participant-item.dragging {
    opacity: 0.4;
    background: var(--bg-card-hover);
    border-style: dashed;
    border-color: var(--accent);
}
.drag-handle {
    cursor: grab;
    color: var(--text-muted);
    font-size: 14px;
    padding: 4px;
    display: flex;
    align-items: center;
    transition: color var(--transition);
}
.drag-handle:hover {
    color: var(--accent);
}
.participant-num {
    width: 28px; height: 28px;
    background: var(--accent-light);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: var(--accent);
    flex-shrink: 0;
}
.participant-name {
    flex: 1;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}
.participant-seed {
    font-size: 12px;
    color: var(--text-muted);
    background: var(--bg-card);
    padding: 2px 8px;
    border-radius: 100px;
    border: 1px solid var(--border);
}

/* ── SIDEBAR ────────────────────────── */
.info-section {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 16px;
}
.info-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 14px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.info-row:last-child { border-bottom: none; }
.info-row-label { color: var(--text-muted); }
.info-row-val { font-weight: 600; color: var(--text-primary); }

/* ── START / CHAMPION ─────────────── */
.champion-card {
    background: linear-gradient(135deg, #1a2744, #1e1a2e);
    border: 1px solid rgba(245,158,11,0.3);
    border-radius: var(--radius);
    padding: 24px;
    text-align: center;
    box-shadow: 0 0 40px rgba(245,158,11,0.1);
    margin-bottom: 16px;
}
.champion-trophy { font-size: 48px; margin-bottom: 8px; }
.champion-label { font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #f59e0b; margin-bottom: 8px; }
.champion-name { font-size: 22px; font-weight: 800; color: #fff; }

.start-card {
    background: var(--accent-light);
    border: 1px solid rgba(59,130,246,0.3);
    border-radius: var(--radius);
    padding: 20px;
    text-align: center;
    margin-bottom: 16px;
}
.start-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
.start-card p { font-size: 13px; color: var(--text-muted); margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div class="show-layout">
    <!-- ── LEFT: Tabs + Content ─────────────────────── -->
    <div>
        <!-- Breadcrumb -->
        <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-muted);margin-bottom:20px">
            <a href="{{ route('tournaments.index') }}" style="color:var(--accent);text-decoration:none">Turnamen Saya</a>
            <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
            <span>{{ $tournament->name }}</span>
        </div>

        <!-- Tabs -->
        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab('bracket', this)" id="tab-bracket">
                <i class="fa-solid fa-sitemap"></i> Bracket
            </button>
            <button class="tab-btn" onclick="switchTab('participants', this)" id="tab-participants">
                <i class="fa-solid fa-users"></i> Peserta
                <span style="background:rgba(255,255,255,0.2);border-radius:100px;padding:1px 8px;font-size:11px">
                    {{ $tournament->participants->count() }}
                </span>
            </button>
        </div>

        <!-- ── TAB: BRACKET ── -->
        <div class="tab-pane active" id="pane-bracket">
            @if($tournament->status === 'pending')
                <div class="card" style="text-align:center;padding:40px 20px">
                    <div style="font-size:48px;margin-bottom:12px">🏓</div>
                    <h3 style="font-size:18px;font-weight:700;margin-bottom:8px">Bracket Belum Di-generate</h3>
                    <p style="color:var(--text-muted);font-size:14px;margin-bottom:20px">
                        Tambahkan minimal 2 peserta, lalu klik tombol di bawah untuk membuat bracket.
                    </p>
                    @if($tournament->participants->count() >= 2)
                        <form method="POST" action="{{ route('tournaments.start', $tournament) }}">
                            @csrf
                            @if($tournament->type === 'round_robin')
                                <div class="form-group" style="margin-bottom:16px;text-align:left;max-width:300px;margin-left:auto;margin-right:auto">
                                    <label class="form-label" style="font-size:13px">Jumlah Pemain per Grup (Opsional)</label>
                                    <input type="number" name="players_per_group" class="form-control" placeholder="Kosongkan untuk 1 grup saja" min="2">
                                </div>
                            @endif
                            <button type="submit" class="btn btn-success" style="font-size:16px;padding:14px 32px">
                                <i class="fa-solid fa-circle-play"></i> Generate Bracket & Mulai Turnamen
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info" style="display:inline-flex;margin:0">
                            <i class="fa-solid fa-circle-info"></i>
                            Tambahkan minimal 2 peserta terlebih dahulu
                        </div>
                    @endif
                </div>

            @else
                <!-- BRACKET VISUALIZATION -->
                @if($tournament->type === 'single_elimination')
                @if($tournament->status === 'finished')
                    @php
                        $finalMatch = $tournament->matches->firstWhere('bracket', 'final');
                        $champion = $finalMatch?->winner;
                    @endphp
                    @if($champion)
                    <div style="background:linear-gradient(135deg,#1a2744,#1e1a2e);border:1px solid rgba(245,158,11,0.3);border-radius:var(--radius);padding:20px;text-align:center;margin-bottom:20px;box-shadow:0 0 40px rgba(245,158,11,0.1)">
                        <div style="font-size:40px;margin-bottom:8px">🏆</div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#f59e0b;margin-bottom:6px">🥇 Juara 1</div>
                        <div style="font-size:24px;font-weight:800">{{ $champion->name }}</div>
                    </div>
                    @endif
                @endif

                <div class="bracket-scroll">
                    <div class="bracket" id="bracket-container">
                        @php
                            $totalR = $matchesByRound->keys()->max() ?? 0;
                        @endphp

                        @foreach($matchesByRound as $round => $matches)
                            @php
                                $isLastRound = ($round == $totalR);
                                $roundLabels = [
                                    1 => 'Babak ' . $round,
                                ];
                                if ($matches->count() == 1 && $isLastRound) {
                                    $label = 'Final';
                                } elseif ($matches->count() == 2 && $isLastRound) {
                                    $label = 'Semifinal';
                                } elseif ($matches->count() == 1) {
                                    $label = 'Final';
                                } elseif ($matches->count() == 2) {
                                    $label = 'Semifinal';
                                } elseif ($matches->count() == 4) {
                                    $label = 'Perempat Final';
                                } else {
                                    $label = 'Babak ' . $round;
                                }
                            @endphp

                            <div class="bracket-round">
                                <div class="bracket-round-label">{{ $label }}</div>
                                <div class="bracket-matches">
                                    @foreach($matches->sortBy('match_number') as $match)
                                        @php
                                            $isFinal = ($match->bracket === 'final' || ($isLastRound && $matches->count() === 1));
                                            $isClickable = $match->status !== 'finished'
                                                && !$match->is_bye
                                                && ($match->participant1_id || $match->participant2_id);
                                            $canInput = $match->participant1_id
                                                && $match->participant2_id
                                                && !$match->is_bye
                                                && !$match->is_rating_processed;
                                        @endphp

                                        <div class="bracket-match-wrap">
                                            <div class="bracket-match {{ $isFinal ? 'final-match' : '' }} {{ $match->status === 'finished' ? 'finished' : '' }} {{ $canInput ? 'clickable' : '' }}"
                                                 id="match-{{ $match->id }}"
                                                 @if($canInput) data-points="{{ $match->point_history ? json_encode($match->point_history) : '' }}" onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->player1_name) }}', '{{ addslashes($match->player2_name) }}', {{ $match->participant1_id }}, {{ $match->participant2_id }}, {{ $match->score1 ?? 'null' }}, {{ $match->score2 ?? 'null' }}, this.dataset.points)" @endif>

                                                @if($isFinal)
                                                    <div class="match-badge">🏆 Final</div>
                                                @endif

                                                <span class="match-num">#{{ $match->match_number }}</span>

                                                <!-- Player 1 -->
                                                <div class="match-player {{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'winner' : ($match->winner_id && $match->winner_id !== $match->participant1_id ? 'loser' : '') }}">
                                                    <span class="match-player-name {{ !$match->participant1_id ? 'tbd' : ($match->is_bye && !$match->participant1_id ? 'bye' : '') }}">
                                                        {{ $match->player1_name }}
                                                    </span>
                                                    @if($match->status === 'finished' && !$match->is_bye)
                                                        <span class="match-player-score">{{ $match->score1 }}</span>
                                                    @endif
                                                    @if($match->winner_id === $match->participant1_id)
                                                        <i class="fa-solid fa-crown" style="color:#f59e0b;font-size:11px;margin-left:4px"></i>
                                                    @endif
                                                </div>

                                                <!-- Player 2 -->
                                                <div class="match-player {{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'winner' : ($match->winner_id && $match->winner_id !== $match->participant2_id ? 'loser' : '') }}">
                                                    <span class="match-player-name {{ !$match->participant2_id ? 'tbd' : '' }}">
                                                        {{ $match->player2_name }}
                                                    </span>
                                                    @if($match->status === 'finished' && !$match->is_bye)
                                                        <span class="match-player-score">{{ $match->score2 }}</span>
                                                    @endif
                                                    @if($match->winner_id === $match->participant2_id)
                                                        <i class="fa-solid fa-crown" style="color:#f59e0b;font-size:11px;margin-left:4px"></i>
                                                    @endif
                                                </div>

                                                @if($match->status === 'finished' && !empty($match->point_history))
                                                    <div style="font-size:10px;color:var(--text-muted);text-align:center;padding:5px 8px;border-top:1px dashed var(--border);font-family:monospace;letter-spacing:0.5px">
                                                        @foreach($match->point_history as $pts)
                                                            {{ $pts['p1'] ?? 0 }}-{{ $pts['p2'] ?? 0 }}@if(!$loop->last), @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($canInput)
                                                    <div style="text-align:center;padding:6px;background:{{ $match->status === 'finished' ? 'var(--bg-card-hover)' : 'var(--accent-light)' }};font-size:11px;color:{{ $match->status === 'finished' ? 'var(--text-muted)' : 'var(--accent)' }};font-weight:600;border-top:1px solid var(--border)">
                                                        <i class="fa-solid fa-pen"></i> {{ $match->status === 'finished' ? 'Edit skor' : 'Input skor' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Third place match -->
                @if($thirdPlaceMatch)
                <div style="margin-top:24px">
                    <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px">
                        🥉 Perebutan Juara 3
                    </div>
                    @php
                        $match = $thirdPlaceMatch;
                        $canInput3 = $match->participant1_id && $match->participant2_id && !$match->is_bye && !$match->is_rating_processed;
                    @endphp
                    <div style="display:flex;gap:16px;align-items:center">
                        <div class="bracket-match third-place-match {{ $canInput3 ? 'clickable' : '' }}"
                             @if($canInput3) data-points="{{ $match->point_history ? json_encode($match->point_history) : '' }}" onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->player1_name) }}', '{{ addslashes($match->player2_name) }}', {{ $match->participant1_id }}, {{ $match->participant2_id }}, {{ $match->score1 ?? 'null' }}, {{ $match->score2 ?? 'null' }}, this.dataset.points)" @endif>

                            <div class="match-badge">🥉 Perebutan Juara 3</div>

                            <div class="match-player {{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                <span class="match-player-name {{ !$match->participant1_id ? 'tbd' : '' }}">
                                    {{ $match->player1_name }}
                                </span>
                                @if($match->status === 'finished')
                                    <span class="match-player-score">{{ $match->score1 }}</span>
                                @endif
                            </div>
                            <div class="match-player {{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                <span class="match-player-name {{ !$match->participant2_id ? 'tbd' : '' }}">
                                    {{ $match->player2_name }}
                                </span>
                                @if($match->status === 'finished')
                                    <span class="match-player-score">{{ $match->score2 }}</span>
                                @endif
                            </div>

                            @if($match->status === 'finished' && !empty($match->point_history))
                                <div style="font-size:10px;color:var(--text-muted);text-align:center;padding:5px 8px;border-top:1px dashed var(--border);font-family:monospace;letter-spacing:0.5px">
                                    @foreach($match->point_history as $pts)
                                        {{ $pts['p1'] ?? 0 }}-{{ $pts['p2'] ?? 0 }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            @endif

                            @if($canInput3)
                                <div style="text-align:center;padding:6px;background:{{ $match->status === 'finished' ? 'var(--bg-card-hover)' : 'var(--purple-light)' }};font-size:11px;color:{{ $match->status === 'finished' ? 'var(--text-muted)' : 'var(--purple)' }};font-weight:600;border-top:1px solid var(--border)">
                                    <i class="fa-solid fa-pen"></i> {{ $match->status === 'finished' ? 'Edit skor' : 'Input skor' }}
                                </div>
                            @endif
                        </div>

                        @if($match->winner_id && $match->winner)
                        <div style="padding:16px 20px;background:var(--purple-light);border:1px solid rgba(168,85,247,0.3);border-radius:var(--radius-sm)">
                            <div style="font-size:11px;color:var(--purple);font-weight:700;letter-spacing:0.5px">🥉 JUARA 3</div>
                            <div style="font-size:16px;font-weight:800;margin-top:4px">{{ $match->winner->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @elseif($tournament->type === 'round_robin')
                    <!-- ROUND ROBIN STANDINGS & MATCHES -->
                    <div style="display:flex;flex-direction:column;gap:24px">
                        <!-- Klasemen -->
                        @foreach($tournament->standings as $group)
                        <div class="card" style="margin-bottom: 24px">
                            <div class="card-header">
                                <h3 class="card-title" style="font-size:16px"><i class="fa-solid fa-list-ol" style="color:var(--accent)"></i> &nbsp;Klasemen (Grup {{ $group->name }})</h3>
                            </div>
                            <div style="overflow-x:auto">
                                <table style="width:100%;border-collapse:collapse;font-size:14px">
                                    <thead>
                                        <tr style="border-bottom:2px solid var(--border);color:var(--text-muted);text-align:left">
                                            <th style="padding:12px;width:40px;text-align:center">#</th>
                                            <th style="padding:12px">Peserta</th>
                                            <th style="padding:12px;text-align:center" title="Main">M</th>
                                            <th style="padding:12px;text-align:center;color:var(--green)" title="Menang">W</th>
                                            <th style="padding:12px;text-align:center;color:var(--red)" title="Kalah">L</th>
                                            <th style="padding:12px;text-align:center" title="Set Won">SW</th>
                                            <th style="padding:12px;text-align:center" title="Set Lost">SL</th>
                                            <th style="padding:12px;text-align:center;color:var(--accent)" title="Set Difference">SD</th>
                                            <th style="padding:12px;text-align:center" title="Point Won">PW</th>
                                            <th style="padding:12px;text-align:center" title="Point Lost">PL</th>
                                            <th style="padding:12px;text-align:center;color:var(--purple)" title="Point Difference">PD</th>
                                            <th style="padding:12px;text-align:center;font-weight:800;color:var(--accent)">Poin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->standings as $index => $row)
                                            <tr style="border-bottom:1px solid var(--border)">
                                                <td style="padding:12px;text-align:center;font-weight:700;color:var(--text-muted)">{{ $index + 1 }}</td>
                                                <td style="padding:12px;font-weight:600;color:var(--text-primary)">
                                                    {{ $row->name }}
                                                    @if($index === 0 && $tournament->status === 'finished')
                                                        <i class="fa-solid fa-crown" style="color:#f59e0b;margin-left:8px"></i>
                                                    @endif
                                                </td>
                                                <td style="padding:12px;text-align:center">{{ $row->played }}</td>
                                                <td style="padding:12px;text-align:center;color:var(--green)">{{ $row->won }}</td>
                                                <td style="padding:12px;text-align:center;color:var(--red)">{{ $row->lost }}</td>
                                                <td style="padding:12px;text-align:center">{{ $row->set_won }}</td>
                                                <td style="padding:12px;text-align:center">{{ $row->set_lost }}</td>
                                                <td style="padding:12px;text-align:center;font-weight:700;color:{{ $row->set_diff > 0 ? 'var(--green)' : ($row->set_diff < 0 ? 'var(--red)' : 'var(--text-muted)') }}">
                                                    {{ $row->set_diff > 0 ? '+'.$row->set_diff : $row->set_diff }}
                                                </td>
                                                <td style="padding:12px;text-align:center">{{ $row->point_won }}</td>
                                                <td style="padding:12px;text-align:center">{{ $row->point_lost }}</td>
                                                <td style="padding:12px;text-align:center;font-weight:700;color:{{ $row->point_diff > 0 ? 'var(--green)' : ($row->point_diff < 0 ? 'var(--red)' : 'var(--text-muted)') }}">
                                                    {{ $row->point_diff > 0 ? '+'.$row->point_diff : $row->point_diff }}
                                                </td>
                                                <td style="padding:12px;text-align:center;font-weight:800;color:var(--accent)">{{ $row->points }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach

                        <!-- Match List -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="font-size:16px"><i class="fa-solid fa-calendar-days" style="color:var(--green)"></i> &nbsp;Jadwal Pertandingan</h3>
                            </div>
                            @if($tournament->type === 'round_robin')
                                @php
                                    $matchesByGroup = $tournament->matches->groupBy('bracket');
                                @endphp
                                @foreach($matchesByGroup as $bracketName => $groupMatches)
                                    @php
                                        $groupLabel = str_replace('round_robin_', '', $bracketName);
                                        $displayLabel = ($groupLabel === 'round_robin' || $groupLabel === '') ? '' : 'Grup ' . $groupLabel;
                                    @endphp
                                    <div style="margin-bottom:24px">
                                        @if($displayLabel)
                                        <h4 style="font-size:14px;font-weight:800;color:var(--accent);margin-bottom:12px;border-bottom:2px solid var(--border);padding-bottom:8px">
                                            <i class="fa-solid fa-users"></i> {{ $displayLabel }}
                                        </h4>
                                        @endif
                                        
                                        @php
                                            $groupMatchesByRound = $groupMatches->groupBy('round');
                                        @endphp
                                        @foreach($groupMatchesByRound as $round => $matches)
                                            <div style="margin-bottom:20px; @if($displayLabel) margin-left:12px; @endif">
                                                <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;padding:8px 16px;background:var(--bg-primary);border-radius:var(--radius-sm);margin-bottom:12px">
                                                    Ronde {{ $round }}
                                                </div>
                                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
                                                    @foreach($matches as $match)
                                                        @php
                                                            $canInput = $match->participant1_id && $match->participant2_id && !$match->is_bye && !$match->is_rating_processed;
                                                        @endphp
                                                        <div class="bracket-match {{ $canInput ? 'clickable' : '' }} {{ $match->status === 'finished' ? 'finished' : '' }}" style="width:100%;margin:0;position:relative;"
                                                             @if($canInput) data-points="{{ $match->point_history ? json_encode($match->point_history) : '' }}" onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->player1_name) }}', '{{ addslashes($match->player2_name) }}', {{ $match->participant1_id }}, {{ $match->participant2_id }}, {{ $match->score1 ?? 'null' }}, {{ $match->score2 ?? 'null' }}, this.dataset.points)" @endif>
                                                            
                                                            <div class="match-player {{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                                                <span class="match-player-name">{{ $match->player1_name }}</span>
                                                                @if($match->status === 'finished')
                                                                    <span class="match-player-score">{{ $match->score1 }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="match-player {{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                                                <span class="match-player-name">{{ $match->player2_name }}</span>
                                                                @if($match->status === 'finished')
                                                                    <span class="match-player-score">{{ $match->score2 }}</span>
                                                                @endif
                                                            </div>

                                                            @if($match->status === 'finished' && !empty($match->point_history))
                                                                <div style="font-size:10px;color:var(--text-muted);text-align:center;padding:5px 8px;border-top:1px dashed var(--border);font-family:monospace;letter-spacing:0.5px">
                                                                    @foreach($match->point_history as $pts)
                                                                        {{ $pts['p1'] ?? 0 }}-{{ $pts['p2'] ?? 0 }}@if(!$loop->last), @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            @if($canInput)
                                                                <div style="text-align:center;padding:6px;background:{{ $match->status === 'finished' ? 'var(--bg-card-hover)' : 'var(--accent-light)' }};font-size:11px;color:{{ $match->status === 'finished' ? 'var(--text-muted)' : 'var(--accent)' }};font-weight:600;border-top:1px solid var(--border)">
                                                                    <i class="fa-solid fa-pen"></i> {{ $match->status === 'finished' ? 'Edit skor' : 'Input skor' }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                @foreach($matchesByRound as $round => $matches)
                                    <div style="margin-bottom:20px">
                                        <div style="font-size:13px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;padding:8px 16px;background:var(--bg-primary);border-radius:var(--radius-sm);margin-bottom:12px">
                                            Ronde {{ $round }}
                                        </div>
                                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
                                            @foreach($matches as $match)
                                                @php
                                                    $canInput = $match->participant1_id && $match->participant2_id && !$match->is_bye && !$match->is_rating_processed;
                                                @endphp
                                                <div class="bracket-match {{ $canInput ? 'clickable' : '' }} {{ $match->status === 'finished' ? 'finished' : '' }}" style="width:100%;margin:0;position:relative;"
                                                     @if($canInput) data-points="{{ $match->point_history ? json_encode($match->point_history) : '' }}" onclick="openScoreModal({{ $match->id }}, '{{ addslashes($match->player1_name) }}', '{{ addslashes($match->player2_name) }}', {{ $match->participant1_id }}, {{ $match->participant2_id }}, {{ $match->score1 ?? 'null' }}, {{ $match->score2 ?? 'null' }}, this.dataset.points)" @endif>
                                                    
                                                    <div class="match-player {{ $match->winner_id === $match->participant1_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                                        <span class="match-player-name">{{ $match->player1_name }}</span>
                                                        @if($match->status === 'finished')
                                                            <span class="match-player-score">{{ $match->score1 }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="match-player {{ $match->winner_id === $match->participant2_id && $match->winner_id ? 'winner' : ($match->winner_id ? 'loser' : '') }}">
                                                        <span class="match-player-name">{{ $match->player2_name }}</span>
                                                        @if($match->status === 'finished')
                                                            <span class="match-player-score">{{ $match->score2 }}</span>
                                                        @endif
                                                    </div>

                                                    @if($match->status === 'finished' && !empty($match->point_history))
                                                        <div style="font-size:10px;color:var(--text-muted);text-align:center;padding:5px 8px;border-top:1px dashed var(--border);font-family:monospace;letter-spacing:0.5px">
                                                            @foreach($match->point_history as $pts)
                                                                {{ $pts['p1'] ?? 0 }}-{{ $pts['p2'] ?? 0 }}@if(!$loop->last), @endif
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if($canInput)
                                                        <div style="text-align:center;padding:6px;background:{{ $match->status === 'finished' ? 'var(--bg-card-hover)' : 'var(--accent-light)' }};font-size:11px;color:{{ $match->status === 'finished' ? 'var(--text-muted)' : 'var(--accent)' }};font-weight:600;border-top:1px solid var(--border)">
                                                            <i class="fa-solid fa-pen"></i> {{ $match->status === 'finished' ? 'Edit skor' : 'Input skor' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                @if($tournament->status === 'ongoing' || $tournament->status === 'finished')
                <div style="margin-top:20px">
                    <form method="POST" action="{{ route('tournaments.reset-bracket', $tournament) }}"
                          onsubmit="return confirm('Reset bracket? Semua skor akan dihapus.')">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-rotate-left"></i> Reset Bracket
                        </button>
                    </form>
                </div>
                @endif
            @endif
        </div>

        <!-- ── TAB: PARTICIPANTS ── -->
        <div class="tab-pane" id="pane-participants">
            @if($tournament->status === 'pending')
                <!-- Add participant forms -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <!-- Single add -->
                    <div class="card">
                        <div class="card-title" style="margin-bottom:16px">
                            <i class="fa-solid fa-user-plus" style="color:var(--accent)"></i> Tambah Peserta
                        </div>
                        <form method="POST" action="{{ route('participants.store', $tournament) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Pilih dari Master Data</label>
                                <select name="player_id" class="form-control">
                                    <option value="">-- Buat Peserta Baru --</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }} (Divisi: {{ $player->division ?? '-' }}, ITR: {{ $player->itr_rating }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Atau Ketik Nama Peserta Baru</label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="cth: Ahmad Fauzi">
                            </div>
                            @if($tournament->seeded)
                            <div class="form-group">
                                <label class="form-label">Nomor Seed</label>
                                <input type="number" name="seed" class="form-control"
                                       placeholder="1, 2, 3..." min="1">
                            </div>
                            @endif
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </form>
                    </div>

                    <!-- Bulk add -->
                    <div class="card">
                        <div class="card-title" style="margin-bottom:16px">
                            <i class="fa-solid fa-list" style="color:var(--green)"></i> Tambah Massal
                        </div>
                        <form method="POST" action="{{ route('participants.bulk-store', $tournament) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Daftar Nama (satu per baris)</label>
                                <textarea name="names" class="form-control" style="min-height:120px"
                                          placeholder="Ahmad Fauzi&#10;Budi Santoso&#10;Citra Dewi&#10;..."></textarea>
                                <div class="form-hint">Copy-paste dari spreadsheet atau ketik satu per baris</div>
                            </div>
                            <button type="submit" class="btn btn-success" style="width:100%">
                                <i class="fa-solid fa-upload"></i> Tambah Semua
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Participants list -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-users" style="color:var(--accent)"></i>
                        Daftar Peserta ({{ $tournament->participants->count() }})
                    </div>
                    @if($tournament->participants->count() >= 2 && $tournament->status === 'pending')
                        <form method="POST" action="{{ route('tournaments.start', $tournament) }}" style="display:flex;align-items:center;gap:8px">
                            @csrf
                            @if($tournament->type === 'round_robin')
                                <input type="number" name="players_per_group" class="form-control form-control-sm" style="width:180px" placeholder="Pemain per Grup (Ops.)" min="2">
                            @endif
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-circle-play"></i> Generate Bracket
                            </button>
                        </form>
                    @endif
                </div>

                @if($tournament->participants->isEmpty())
                    <div class="empty-state">
                        <i class="fa-solid fa-users"></i>
                        <h3>Belum ada peserta</h3>
                        <p>Tambahkan peserta menggunakan form di atas</p>
                    </div>
                @else
                    <div class="participants-list" id="participants-sortable-list">
                        @foreach($tournament->participants as $i => $participant)
                            <div class="participant-item {{ ($tournament->status === 'pending' && $tournament->seeded) ? 'draggable' : '' }}" 
                                 @if($tournament->status === 'pending' && $tournament->seeded) draggable="true" @endif 
                                 data-id="{{ $participant->id }}">
                                @if($tournament->status === 'pending' && $tournament->seeded)
                                    <div class="drag-handle" title="Tarik untuk mengurutkan">
                                        <i class="fa-solid fa-grip-lines"></i>
                                    </div>
                                @endif
                                <div class="participant-num">{{ $i + 1 }}</div>
                                <div class="participant-name">
                                    {{ $participant->name }}
                                    @if($participant->player)
                                        <div style="display:inline-flex;gap:4px;margin-left:8px;vertical-align:middle">
                                            @if($participant->player->division)
                                                <span class="badge badge-gray" style="font-size:10px;padding:2px 6px">{{ $participant->player->division }}</span>
                                            @endif
                                            <span class="badge badge-blue" style="font-size:10px;padding:2px 6px">
                                                <i class="fa-solid fa-star"></i> {{ $participant->player->itr_rating }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @if($tournament->seeded)
                                    <span class="participant-seed">Seed #{{ $participant->seed ?? ($i + 1) }}</span>
                                @endif
                                @if($tournament->status === 'pending')
                                    <form method="POST" action="{{ route('participants.destroy', [$tournament, $participant]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon btn-sm"
                                                onclick="return confirm('Hapus {{ $participant->name }}?')"
                                                title="Hapus">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── RIGHT SIDEBAR ─────────────────────────────── -->
    <div>
        <!-- Tournament Info -->
        <div class="info-section">
            <div class="info-title">📋 Info Turnamen</div>
            <div class="info-row">
                <span class="info-row-label">Status</span>
                <span class="badge badge-{{ $tournament->status_color }}">{{ $tournament->status_label }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Tanggal</span>
                <span class="info-row-val">{{ $tournament->formatted_date_range }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Peserta</span>
                <span class="info-row-val">{{ $tournament->participants->count() }} orang</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Format</span>
                <span class="info-row-val">{{ $tournament->type === 'round_robin' ? 'Round Robin' : 'Single Elimination' }}</span>
            </div>
            <div class="info-row">
                <span class="info-row-label">Total Ronde</span>
                <span class="info-row-val">{{ $totalRounds ?: '-' }}</span>
            </div>
            @if($totalRounds > 0)
            <div class="info-row">
                <span class="info-row-label">Total Match</span>
                <span class="info-row-val">{{ $tournament->matches->count() }}</span>
            </div>
            @endif
            @if($tournament->type === 'single_elimination')
            <div class="info-row">
                <span class="info-row-label">Juara 3</span>
                <span class="info-row-val">{{ $tournament->third_place_match ? 'Ya' : 'Tidak' }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-row-label">Seeding</span>
                <span class="info-row-val">{{ $tournament->seeded ? 'Ya' : 'Tidak' }}</span>
            </div>
            @if($tournament->description)
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);font-size:13px;color:var(--text-muted);line-height:1.6">
                {{ $tournament->description }}
            </div>
            @endif
        </div>

        <!-- Progress -->
        @if($tournament->status !== 'pending')
        @php
            $totalMatches  = $tournament->matches->where('bracket', '!=', 'third_place')->where('is_bye', false)->count();
            $doneMatches   = $tournament->matches->where('status', 'finished')->where('is_bye', false)->count();
            $pct           = $totalMatches > 0 ? round(($doneMatches / $totalMatches) * 100) : 0;
        @endphp
        <div class="info-section">
            <div class="info-title">📊 Progress</div>
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px">
                <span style="color:var(--text-muted)">Match Selesai</span>
                <span style="font-weight:700">{{ $doneMatches }}/{{ $totalMatches }}</span>
            </div>
            <div style="height:8px;background:var(--border);border-radius:100px;overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,var(--accent),var(--green));border-radius:100px;transition:width 0.5s ease"></div>
            </div>
            <div style="text-align:right;font-size:12px;color:var(--text-muted);margin-top:6px">{{ $pct }}%</div>
        </div>
        @endif

        <!-- Quick Actions -->
        @if($tournament->status === 'pending' && $tournament->participants->count() >= 2)
        <div class="info-section" style="background:var(--accent-light);border-color:rgba(59,130,246,0.3)">
            <div class="info-title" style="color:var(--accent)">⚡ Siap Dimulai!</div>
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px">{{ $tournament->participants->count() }} peserta sudah terdaftar. Generate bracket sekarang!</p>
            <form method="POST" action="{{ route('tournaments.start', $tournament) }}">
                @csrf
                @if($tournament->type === 'round_robin')
                    <div class="form-group" style="margin-bottom:12px;text-align:left">
                        <label class="form-label" style="font-size:12px">Pemain per Grup (Opsional)</label>
                        <input type="number" name="players_per_group" class="form-control" placeholder="Kosongkan untuk 1 grup saja" min="2">
                    </div>
                @endif
                <button type="submit" class="btn btn-success" style="width:100%">
                    <i class="fa-solid fa-circle-play"></i> Mulai Turnamen
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- ── SCORE MODAL ────────────────────────────────── -->
<div class="modal-overlay" id="score-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><i class="fa-solid fa-pen" style="color:var(--accent)"></i> &nbsp;Input Skor Pertandingan</div>
            <button class="modal-close" onclick="closeModal('score-modal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="score-form" method="POST">
            @csrf
            @method('PATCH')

            <div class="modal-body">
                <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px">
                    <div style="font-size:12px;color:var(--text-muted);text-align:center;margin-bottom:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase">Hasil Pertandingan</div>

                    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:12px;align-items:center">
                        <div style="text-align:center">
                            <div id="modal-p1-name" style="font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text-primary)"></div>
                            <input type="number" name="score1" id="modal-score1" class="form-control"
                                   style="text-align:center;font-size:22px;font-weight:800;padding:12px"
                                   min="0" max="99" placeholder="0" required onchange="updatePointInputs()">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Set Menang</div>
                        </div>
                        <div style="text-align:center;font-size:18px;font-weight:800;color:var(--text-muted);padding-top:10px">VS</div>
                        <div style="text-align:center">
                            <div id="modal-p2-name" style="font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text-primary)"></div>
                            <input type="number" name="score2" id="modal-score2" class="form-control"
                                   style="text-align:center;font-size:22px;font-weight:800;padding:12px"
                                   min="0" max="99" placeholder="0" required onchange="updatePointInputs()">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Set Menang</div>
                        </div>
                    </div>

                    <!-- Dynamic Point Score Inputs -->
                    <div id="point-inputs-container" style="margin-top:20px;border-top:1px dashed var(--border);padding-top:16px;display:none">
                        <div style="font-size:12px;color:var(--text-muted);text-align:center;margin-bottom:12px;font-weight:600">POIN PER SET (Opsional)</div>
                        <div id="point-inputs-list" style="display:flex;flex-direction:column;gap:10px"></div>
                    </div>
                </div>

                <div class="form-group" style="margin:0">
                    <label class="form-label">Pemenang</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <label id="winner-option-1" style="cursor:pointer">
                            <input type="radio" name="winner_id" id="modal-winner1" style="display:none" required>
                            <div class="winner-btn" style="border:2px solid var(--border);border-radius:var(--radius-sm);padding:12px;text-align:center;font-size:14px;font-weight:600;transition:all 0.2s;color:var(--text-secondary)">
                                <i class="fa-solid fa-crown" style="color:#f59e0b;margin-right:6px"></i>
                                <span id="winner-btn-1-name"></span>
                            </div>
                        </label>
                        <label id="winner-option-2" style="cursor:pointer">
                            <input type="radio" name="winner_id" id="modal-winner2" style="display:none">
                            <div class="winner-btn" style="border:2px solid var(--border);border-radius:var(--radius-sm);padding:12px;text-align:center;font-size:14px;font-weight:600;transition:all 0.2s;color:var(--text-secondary)">
                                <i class="fa-solid fa-crown" style="color:#f59e0b;margin-right:6px"></i>
                                <span id="winner-btn-2-name"></span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('score-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Simpan Skor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('pane-' + tab).classList.add('active');
}

let globalPointHistory = null;

function openScoreModal(matchId, p1Name, p2Name, p1Id, p2Id, s1, s2, pointHistoryRaw) {
    globalPointHistory = null;
    if (pointHistoryRaw) {
        try {
            globalPointHistory = typeof pointHistoryRaw === 'string' ? JSON.parse(pointHistoryRaw) : pointHistoryRaw;
        } catch (e) {
            console.error("Failed to parse point history", e);
        }
    }

    const form = document.getElementById('score-form');
    form.action = `/matches/${matchId}`;

    document.getElementById('modal-p1-name').textContent = p1Name;
    document.getElementById('modal-p2-name').textContent = p2Name;
    document.getElementById('winner-btn-1-name').textContent = p1Name;
    document.getElementById('winner-btn-2-name').textContent = p2Name;

    document.getElementById('modal-winner1').value = p1Id;
    document.getElementById('modal-winner2').value = p2Id;

    document.getElementById('modal-score1').value = s1 !== null ? s1 : '';
    document.getElementById('modal-score2').value = s2 !== null ? s2 : '';

    document.getElementById('modal-winner1').checked = false;
    document.getElementById('modal-winner2').checked = false;
    highlightWinner(0); // reset visual
    
    if (s1 !== null && s2 !== null) {
        if (s1 > s2) {
            document.getElementById('modal-winner1').checked = true;
            highlightWinner(1);
        } else if (s2 > s1) {
            document.getElementById('modal-winner2').checked = true;
            highlightWinner(2);
        }
    }

    updatePointInputs(); // Initialize point inputs

    openModal('score-modal');
    setTimeout(() => document.getElementById('modal-score1').focus(), 200);
}

function updatePointInputs() {
    const s1 = parseInt(document.getElementById('modal-score1').value) || 0;
    const s2 = parseInt(document.getElementById('modal-score2').value) || 0;
    
    // Auto-suggest winner based on sets
    if (s1 > s2) {
        document.getElementById('modal-winner1').checked = true;
        highlightWinner(1);
    } else if (s2 > s1) {
        document.getElementById('modal-winner2').checked = true;
        highlightWinner(2);
    }

    // Generate dynamic point inputs
    const totalSets = s1 + s2;
    const container = document.getElementById('point-inputs-container');
    const list = document.getElementById('point-inputs-list');
    
    if (totalSets <= 0 || totalSets > 9) { // Limit max sets
        container.style.display = 'none';
        list.innerHTML = '';
        return;
    }
    
    container.style.display = 'block';
    
    const existingP1 = Array.from(document.querySelectorAll('input[name="points_p1[]"]')).map(el => el.value);
    const existingP2 = Array.from(document.querySelectorAll('input[name="points_p2[]"]')).map(el => el.value);
    
    list.innerHTML = '';
    for (let i = 0; i < totalSets; i++) {
        let val1 = existingP1[i] !== undefined ? existingP1[i] : '';
        let val2 = existingP2[i] !== undefined ? existingP2[i] : '';

        // Fill with DB data on first load if available
        if (globalPointHistory && globalPointHistory[i] && existingP1.length === 0) {
            val1 = globalPointHistory[i].p1;
            val2 = globalPointHistory[i].p2;
        }

        const row = document.createElement('div');
        row.style.display = 'grid';
        row.style.gridTemplateColumns = '1fr auto 1fr';
        row.style.gap = '12px';
        row.style.alignItems = 'center';
        row.innerHTML = `
            <div style="position:relative">
                <span style="position:absolute;left:-24px;top:50%;transform:translateY(-50%);font-size:10px;color:var(--text-muted);font-weight:700">S${i+1}</span>
                <input type="number" name="points_p1[]" class="form-control" style="text-align:center;padding:6px;font-size:15px;font-weight:700" min="0" max="99" value="${val1}">
            </div>
            <div style="color:var(--text-muted);font-size:12px;font-weight:700">-</div>
            <div>
                <input type="number" name="points_p2[]" class="form-control" style="text-align:center;padding:6px;font-size:15px;font-weight:700" min="0" max="99" value="${val2}">
            </div>
        `;
        list.appendChild(row);
    }
}

function highlightWinner(n) {
    document.querySelectorAll('.winner-btn').forEach((btn, i) => {
        btn.style.borderColor = (i + 1 === n) ? 'var(--green)' : 'var(--border)';
        btn.style.background  = (i + 1 === n) ? 'var(--green-light)' : '';
        btn.style.color       = (i + 1 === n) ? 'var(--green)' : 'var(--text-secondary)';
    });
}

// Winner radio visual
document.querySelectorAll('input[name="winner_id"]').forEach((radio, i) => {
    radio.addEventListener('change', () => highlightWinner(i + 1));
});

document.querySelectorAll('label[id^="winner-option"]').forEach((lbl, i) => {
    lbl.addEventListener('click', () => highlightWinner(i + 1));
});

// Drag and drop sorting for participants
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('participants-sortable-list');
    if (!list) return;

    let dragEl = null;

    const items = list.querySelectorAll('.participant-item.draggable');
    if (items.length === 0) return;

    items.forEach(item => {
        item.addEventListener('dragstart', (e) => {
            dragEl = item;
            item.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', item.innerHTML);
        });

        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            const bounding = item.getBoundingClientRect();
            const offset = bounding.y + bounding.height / 2;
            
            if (e.clientY - offset > 0) {
                item.style.borderBottom = '2px solid var(--accent)';
                item.style.borderTop = '';
            } else {
                item.style.borderTop = '2px solid var(--accent)';
                item.style.borderBottom = '';
            }
        });

        item.addEventListener('dragleave', () => {
            item.style.borderTop = '';
            item.style.borderBottom = '';
        });

        item.addEventListener('dragend', () => {
            item.classList.remove('dragging');
            items.forEach(el => {
                el.style.borderTop = '';
                el.style.borderBottom = '';
            });
        });

        item.addEventListener('drop', (e) => {
            e.preventDefault();
            item.style.borderTop = '';
            item.style.borderBottom = '';
            
            if (dragEl !== item) {
                const bounding = item.getBoundingClientRect();
                const offset = bounding.y + bounding.height / 2;
                
                if (e.clientY - offset > 0) {
                    item.after(dragEl);
                } else {
                    item.before(dragEl);
                }
                
                saveParticipantsOrder();
            }
        });
    });

    function saveParticipantsOrder() {
        const sortedItems = Array.from(list.querySelectorAll('.participant-item'));
        const order = sortedItems.map(item => item.dataset.id);

        sortedItems.forEach((item, index) => {
            const numEl = item.querySelector('.participant-num');
            if (numEl) numEl.textContent = index + 1;
            
            const seedEl = item.querySelector('.participant-seed');
            if (seedEl) seedEl.textContent = `Seed #${index + 1}`;
        });

        fetch('{{ route("participants.sort", $tournament) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Error saving participant order:', err);
            alert('Gagal menyimpan urutan peserta. Silakan coba lagi.');
            window.location.reload();
        });
    }
});
</script>
@endpush
