@extends('layouts.app')

@section('title', 'Pertandingan')
@section('page-title', 'Pertandingan')

@push('styles')
<style>
.page-header {
    margin-bottom: 28px;
}
.page-header h1 {
    font-size: 28px;
    font-weight: 800;
    background: linear-gradient(135deg, #f1f5f9, #94a3b8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 6px;
}
.page-header p {
    color: var(--text-muted);
    font-size: 15px;
}

.match-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.match-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all var(--transition);
}

.match-card:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.match-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tournament-name {
    font-size: 13px;
    color: var(--accent);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.match-details {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

.vs-badge {
    background: var(--bg-body);
    color: var(--text-muted);
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.score-box {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 20px;
    font-weight: 800;
}

.score {
    padding: 8px 16px;
    border-radius: 8px;
    background: var(--bg-body);
    min-width: 48px;
    text-align: center;
}

.score.winner {
    background: var(--green-light);
    color: var(--green);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.status-badge.pending { background: var(--yellow-light); color: var(--yellow); }
.status-badge.finished { background: var(--green-light); color: var(--green); }

.pagination-wrap {
    margin-top: 28px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .match-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    .score-box {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>⚔️ Pertandingan</h1>
    <p>Daftar semua kegiatan pertandingan, peserta, dan hasil akhir pertandingan Anda.</p>
</div>

@if($matches->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fa-solid fa-table-tennis-paddle-ball"></i>
            <h3>Belum ada pertandingan</h3>
            <p>Pertandingan akan muncul setelah turnamen dimulai.</p>
        </div>
    </div>
@else
    <div class="match-list">
        @foreach($matches as $match)
        <div class="match-card">
            <div class="match-info">
                <div class="tournament-name">
                    <i class="fa-solid fa-trophy"></i> {{ $match->tournament->name }} 
                    <span class="text-muted" style="margin-left: 8px;">
                        @if($match->bracket === 'third_place')
                            (Perebutan Juara 3)
                        @else
                            (Round {{ $match->round }})
                        @endif
                    </span>
                </div>
                <div class="match-details">
                    <span @if($match->winner_id && $match->winner_id == $match->participant1_id) style="color: var(--green);" @endif>
                        {{ $match->player1_name }}
                    </span>
                    <span class="vs-badge">VS</span>
                    <span @if($match->winner_id && $match->winner_id == $match->participant2_id) style="color: var(--green);" @endif>
                        {{ $match->player2_name }}
                    </span>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 24px;">
                @if($match->status === 'finished')
                    <div class="score-box">
                        <div class="score {{ $match->winner_id == $match->participant1_id ? 'winner' : '' }}">{{ $match->score1 ?? 0 }}</div>
                        <span class="text-muted">-</span>
                        <div class="score {{ $match->winner_id == $match->participant2_id ? 'winner' : '' }}">{{ $match->score2 ?? 0 }}</div>
                    </div>
                @else
                    <span class="status-badge pending">Belum Main</span>
                @endif
                
                <a href="{{ route('tournaments.show', $match->tournament_id) }}" class="btn btn-outline btn-sm">
                    Lihat Turnamen
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrap">
        {{ $matches->links() }}
    </div>
@endif
@endsection
