@extends('layouts.app')

@section('title', 'Detail Pertemuan: ' . $friendlyMatch->ptm_name)
@section('page-title', 'Detail Pertemuan')

@push('styles')
<style>
.summary-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.summary-info h2 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
    color: var(--text-primary);
}
.summary-meta {
    font-size: 14px;
    color: var(--text-muted);
    display: flex;
    gap: 16px;
    margin-bottom: 12px;
}
.summary-score {
    text-align: center;
    background: var(--bg-secondary);
    padding: 16px 24px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
}
.score-numbers {
    font-size: 32px;
    font-weight: 800;
    display: flex;
    gap: 16px;
    align-items: center;
}
.score-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
    font-weight: 600;
    text-transform: uppercase;
}
.score-win { color: var(--green); }
.score-loss { color: var(--red); }
.score-draw { color: var(--yellow); }

.grid-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 24px;
}
@media (max-width: 992px) {
    .grid-layout { grid-template-columns: 1fr; }
}

.table {
    width: 100%;
    border-collapse: collapse;
}
.table th, .table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.table th {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    background: rgba(0,0,0,0.2);
}
.table td { font-size: 14px; }
.action-btn {
    background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; transition: color 0.2s;
}
.action-btn:hover { color: var(--red); }
</style>
@endpush

@section('content')
<div class="summary-card">
    <div class="summary-info">
        <h2>🤝 Lawan {{ $friendlyMatch->ptm_name }}</h2>
        <div class="summary-meta">
            <span><i class="fa-regular fa-calendar"></i> {{ $friendlyMatch->match_date->format('d M Y') }}</span>
            <span><i class="fa-solid fa-gamepad"></i> {{ $friendlyMatch->games->count() }} Partai</span>
        </div>
        @if($friendlyMatch->notes)
            <div style="font-size: 14px; color: var(--text-secondary); background: rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 6px; display: inline-block;">
                <i class="fa-solid fa-note-sticky"></i> {{ $friendlyMatch->notes }}
            </div>
        @endif
        
        <div style="margin-top: 16px;">
            <a href="{{ route('friendly-matches.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
    
    <div class="summary-score">
        <div class="score-numbers">
            <span class="{{ $friendlyMatch->total_score_home > $friendlyMatch->total_score_away ? 'score-win' : ($friendlyMatch->total_score_home < $friendlyMatch->total_score_away ? 'score-loss' : 'score-draw') }}">{{ $friendlyMatch->total_score_home }}</span>
            <span style="color:var(--text-muted); font-size: 24px;">-</span>
            <span class="{{ $friendlyMatch->total_score_away > $friendlyMatch->total_score_home ? 'score-win' : ($friendlyMatch->total_score_away < $friendlyMatch->total_score_home ? 'score-loss' : 'score-draw') }}">{{ $friendlyMatch->total_score_away }}</span>
        </div>
        <div class="score-labels">
            <span>Tim Anda</span>
            <span>Lawan</span>
        </div>
    </div>
</div>

<div class="grid-layout">
    <!-- Form Tambah Partai -->
    <div class="card" style="align-self: start;">
        <h3 class="card-title" style="margin-bottom: 16px;">Tambah Partai</h3>
        <form action="{{ route('friendly-matches.games.store', $friendlyMatch) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Pemain Anda</label>
                <select name="player_id" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                    @endforeach
                </select>
                @error('player_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Pemain Lawan</label>
                <input type="text" name="opponent_name" class="form-control" required>
                @error('opponent_name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Skor (Game)</label>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <input type="number" name="score_home" class="form-control" placeholder="Anda" min="0" required style="text-align: center;">
                    <span style="font-weight: 800; color:var(--text-muted);">-</span>
                    <input type="number" name="score_away" class="form-control" placeholder="Lawan" min="0" required style="text-align: center;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </form>
    </div>

    <!-- Daftar Partai -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 16px;">Daftar Pertandingan</h3>
        @if($friendlyMatch->games->isEmpty())
            <div class="empty-state" style="padding: 30px;">
                <i class="fa-solid fa-table-tennis-paddle-ball" style="font-size: 32px;"></i>
                <p>Belum ada data partai yang dimainkan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pemain Anda</th>
                            <th></th>
                            <th>Pemain Lawan</th>
                            <th style="text-align:center;">Skor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($friendlyMatch->games as $index => $game)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color:var(--accent);">{{ $game->player ? $game->player->name : 'Dihapus' }}</td>
                            <td class="text-muted" style="font-size: 11px; font-weight:700;">VS</td>
                            <td style="font-weight: 600; color:var(--red);">{{ $game->opponent_name }}</td>
                            <td style="text-align:center; font-weight:800; font-size:16px;">
                                <span class="{{ $game->score_home > $game->score_away ? 'score-win' : ($game->score_home < $game->score_away ? 'score-loss' : '') }}">{{ $game->score_home }}</span>
                                -
                                <span class="{{ $game->score_away > $game->score_home ? 'score-win' : ($game->score_away < $game->score_home ? 'score-loss' : '') }}">{{ $game->score_away }}</span>
                            </td>
                            <td>
                                <form action="{{ route('friendly-matches.games.destroy', [$friendlyMatch, $game]) }}" method="POST" onsubmit="return confirm('Hapus partai ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
