@extends('layouts.app')

@section('title', 'Pertandingan Persahabatan')
@section('page-title', 'Pertandingan Persahabatan')

@section('topbar-actions')
    <a href="{{ route('friendly-matches.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Pertandingan
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

.table-wrapper {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}

.table th, .table td {
    padding: 16px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.table th {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(0,0,0,0.2);
}

.table tbody tr {
    transition: background var(--transition);
}

.table tbody tr:hover {
    background: var(--bg-card-hover);
}

.table td {
    font-size: 14px;
    color: var(--text-primary);
}

.score-cell {
    font-weight: 700;
    font-size: 16px;
}

.score-win {
    color: var(--green);
}

.score-loss {
    color: var(--red);
}

.score-draw {
    color: var(--yellow);
}

.action-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 16px;
    transition: color var(--transition);
    padding: 4px;
}

.action-btn:hover {
    color: var(--red);
}

.pagination-wrap {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>🤝 Pertandingan Persahabatan</h1>
    <p>Catat dan kelola hasil pertandingan persahabatan Anda dengan PTM lain.</p>
</div>

@if($matches->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fa-solid fa-handshake"></i>
            <h3>Belum ada pertandingan persahabatan</h3>
            <p>Mulai catat hasil latih tanding dan pertandingan persahabatan Anda.</p>
            <a href="{{ route('friendly-matches.create') }}" class="btn btn-primary" style="margin-top: 12px;">
                <i class="fa-solid fa-plus"></i> Tambah Data Pertama
            </a>
        </div>
    </div>
@else
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama PTM</th>
                    <th>Keterangan</th>
                    <th style="text-align: center;">Total Partai Menang (Anda - Lawan)</th>
                    <th>Hasil</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $match)
                <tr>
                    <td>{{ $match->match_date->format('d M Y') }}</td>
                    <td style="font-weight: 600;">{{ $match->ptm_name }}</td>
                    <td class="text-muted">{{ $match->notes ?: '-' }}</td>
                    <td class="score-cell" style="text-align: center;">
                        <span class="{{ $match->total_score_home > $match->total_score_away ? 'score-win' : ($match->total_score_home < $match->total_score_away ? 'score-loss' : 'score-draw') }}">
                            {{ $match->total_score_home }}
                        </span>
                        -
                        <span class="{{ $match->total_score_away > $match->total_score_home ? 'score-win' : ($match->total_score_away < $match->total_score_home ? 'score-loss' : 'score-draw') }}">
                            {{ $match->total_score_away }}
                        </span>
                    </td>
                    <td>
                        @if($match->total_score_home > $match->total_score_away)
                            <span class="badge badge-green">Menang</span>
                        @elseif($match->total_score_home < $match->total_score_away)
                            <span class="badge badge-gray" style="color:var(--red);">Kalah</span>
                        @elseif($match->total_score_home == 0 && $match->total_score_away == 0)
                            <span class="badge badge-gray">Belum Main</span>
                        @else
                            <span class="badge badge-yellow">Seri</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('friendly-matches.show', $match) }}" class="btn btn-outline btn-sm" style="margin-right: 8px;">Detail</a>
                        <form action="{{ route('friendly-matches.destroy', $match) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertandingan ini?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $matches->links() }}
    </div>
@endif
@endsection
