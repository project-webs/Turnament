@extends('layouts.app')

@section('title', 'Edit Turnamen')
@section('page-title', 'Edit Turnamen')

@section('content')
@push('styles')
<style>
.option-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.option-card {
    border: 2px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px;
    cursor: pointer;
    transition: all var(--transition);
    text-align: center;
    position: relative;
}
.option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}
.option-card:hover { border-color: var(--accent); background: var(--accent-light); }
.option-card:has(input[type="radio"]:checked) { border-color: var(--accent); background: var(--accent-light); }
.option-card i { font-size: 24px; margin-bottom: 8px; display: block; }
.option-card span { font-size: 13px; font-weight: 600; }
</style>
@endpush

<div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-muted);margin-bottom:24px">
    <a href="{{ route('tournaments.index') }}" style="color:var(--accent);text-decoration:none">Turnamen Saya</a>
    <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
    <a href="{{ route('tournaments.show', $tournament) }}" style="color:var(--accent);text-decoration:none">{{ $tournament->name }}</a>
    <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
    <span>Edit</span>
</div>

<div style="max-width:640px">
    <form method="POST" action="{{ route('tournaments.update', $tournament) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-pen" style="color:var(--accent)"></i> &nbsp;Edit Turnamen</h2>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Nama Turnamen *</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name', $tournament->name) }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" class="form-control">{{ old('description', $tournament->description) }}</textarea>
            </div>

            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                           value="{{ old('start_date', $tournament->start_date?->format('Y-m-d')) }}">
                    @error('start_date')
                        <div class="form-error" style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                           value="{{ old('end_date', $tournament->end_date?->format('Y-m-d')) }}">
                    @error('end_date')
                        <div class="form-error" style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="divider">

            <div class="form-group">
                <label class="form-label">Format Bracket</label>
                <div class="option-cards">
                    <label class="option-card">
                        <input type="radio" name="type" value="single_elimination" id="type_se"
                               {{ old('type', $tournament->type) === 'single_elimination' ? 'checked' : '' }}
                               onchange="updatePreview()">
                        <i class="fa-solid fa-sitemap" style="color:var(--accent)"></i>
                        <span>Single Elimination</span>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="type" value="round_robin" id="type_rr"
                               {{ old('type', $tournament->type) === 'round_robin' ? 'checked' : '' }}
                               onchange="updatePreview()">
                        <i class="fa-solid fa-table-list" style="color:var(--green)"></i>
                        <span>Round Robin</span>
                    </label>
                </div>
                <div class="form-hint" id="format-hint">
                    {{ old('type', $tournament->type) === 'round_robin' ? 'Semua peserta akan saling bertanding (Setengah Kompetisi). Menang = 1 Poin.' : 'Peserta yang kalah langsung gugur. Format paling umum untuk turnamen.' }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Opsi Tambahan</label>

                <div class="toggle-group" style="margin-bottom: 14px" id="third_place_container">
                    <label class="toggle">
                        <input type="checkbox" name="third_place_match" value="1"
                               {{ old('third_place_match', $tournament->third_place_match) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <div>
                        <div style="font-size:14px;font-weight:600;color:var(--text-primary)">Pertandingan Perebutan Juara 3</div>
                        <div class="form-hint" style="margin:0">Semifinal losers bermain untuk posisi 3</div>
                    </div>
                </div>

                <div class="toggle-group">
                    <label class="toggle">
                        <input type="checkbox" name="seeded" value="1"
                               {{ old('seeded', $tournament->seeded) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <div>
                        <div style="font-size:14px;font-weight:600;color:var(--text-primary)">Gunakan Seed</div>
                        <div class="form-hint" style="margin:0">Peserta diurutkan berdasarkan nomor seed</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px">
            <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
function updatePreview() {
    const format = document.querySelector('input[name="type"]:checked').value;

    if (format === 'single_elimination') {
        document.getElementById('format-hint').textContent = 'Peserta yang kalah langsung gugur. Format paling umum untuk turnamen.';
        document.getElementById('third_place_container').style.display = 'flex';
    } else {
        document.getElementById('format-hint').textContent = 'Semua peserta akan saling bertanding (Setengah Kompetisi). Menang = 1 Poin.';
        document.getElementById('third_place_container').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
