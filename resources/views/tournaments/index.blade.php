@extends('layouts.app')

@section('title', 'Turnamen Saya')
@section('page-title', 'Turnamen Saya')

@section('topbar-actions')
    <a href="{{ route('tournaments.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Buat Turnamen
    </a>
@endsection

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

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all var(--transition);
}
.stat-card:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-icon.blue   { background: var(--accent-light); color: var(--accent); }
.stat-icon.green  { background: var(--green-light);  color: var(--green); }
.stat-icon.yellow { background: var(--yellow-light); color: var(--yellow); }
.stat-icon.purple { background: var(--purple-light); color: var(--purple); }
.stat-value { font-size: 26px; font-weight: 800; line-height: 1; }
.stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-top: 4px; }

.tournament-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
}

.tournament-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px;
    transition: all var(--transition);
    cursor: pointer;
    text-decoration: none;
    display: block;
    position: relative;
    overflow: hidden;
}
.tournament-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--purple));
    opacity: 0;
    transition: opacity var(--transition);
}
.tournament-card:hover {
    border-color: var(--accent);
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.4);
}
.tournament-card:hover::before { opacity: 1; }

.tc-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 14px;
}
.tc-name {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.3;
    margin-bottom: 4px;
}
.tc-desc {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.tc-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px 16px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}
.tc-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-muted);
}
.tc-meta-item i { font-size: 12px; }
.tc-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
}

.pagination-wrap {
    margin-top: 28px;
    display: flex;
    justify-content: center;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>🏆 Turnamen Saya</h1>
    <p>Kelola semua turnamen tenis meja Anda di satu tempat.</p>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-trophy"></i></div>
        <div>
            <div class="stat-value">{{ $tournaments->total() }}</div>
            <div class="stat-label">Total Turnamen</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-play"></i></div>
        <div>
            <div class="stat-value">{{ auth()->user()->tournaments()->where('status','ongoing')->count() }}</div>
            <div class="stat-label">Sedang Berlangsung</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ auth()->user()->tournaments()->where('status','pending')->count() }}</div>
            <div class="stat-label">Belum Dimulai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fa-solid fa-flag-checkered"></i></div>
        <div>
            <div class="stat-value">{{ auth()->user()->tournaments()->where('status','finished')->count() }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
</div>

@if($tournaments->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fa-solid fa-trophy"></i>
            <h3>Belum ada turnamen</h3>
            <p>Buat turnamen pertama Anda dan mulai kelola bracket!</p>
            <a href="{{ route('tournaments.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Buat Turnamen Pertama
            </a>
        </div>
    </div>
@else
    <div class="tournament-grid">
        @foreach($tournaments as $tournament)
        <div onclick="window.location='{{ route('tournaments.show', $tournament) }}'" class="tournament-card" id="tc-{{ $tournament->id }}">
            <div class="tc-header">
                <div>
                    <div class="tc-name">{{ $tournament->name }}</div>
                    @if($tournament->description)
                        <div class="tc-desc">{{ $tournament->description }}</div>
                    @endif
                </div>
                <span class="badge badge-{{ $tournament->status_color }}">
                    <i class="fa-solid fa-circle" style="font-size:7px"></i>
                    {{ $tournament->status_label }}
                </span>
            </div>

            <div class="tc-meta">
                @if($tournament->start_date || $tournament->end_date)
                <div class="tc-meta-item">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>{{ $tournament->formatted_date_range }}</span>
                </div>
                @endif
                <div class="tc-meta-item">
                    <i class="fa-solid fa-users"></i>
                    <span>{{ $tournament->participants_count ?? $tournament->participant_count }} peserta</span>
                </div>
                <div class="tc-meta-item">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>{{ $tournament->type === 'round_robin' ? 'Round Robin' : 'Single Elimination' }}</span>
                </div>
                @if($tournament->type === 'single_elimination' && $tournament->third_place_match)
                <div class="tc-meta-item">
                    <i class="fa-solid fa-medal"></i>
                    <span>Juara 3</span>
                </div>
                @endif
            </div>

            <div class="tc-actions" onclick="event.stopPropagation()">
                <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-eye"></i> Lihat Bracket
                </a>
                @if($tournament->status === 'pending')
                    <a href="{{ route('tournaments.edit', $tournament) }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                @endif
                <form method="POST" action="{{ route('tournaments.destroy', $tournament) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus turnamen ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrap">
        {{ $tournaments->links() }}
    </div>
@endif
@endsection
