@extends('layouts.app')

@section('title', 'Buat Turnamen Baru')
@section('page-title', 'Buat Turnamen Baru')

@push('styles')
<style>
.create-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) {
    .create-grid { grid-template-columns: 1fr; }
}

.preview-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    position: sticky;
    top: 88px;
}
.preview-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
}
.preview-bracket-icon {
    text-align: center;
    padding: 20px 0;
    font-size: 48px;
}
.preview-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}
.preview-stat:last-child { border-bottom: none; }
.preview-stat-label { color: var(--text-muted); }
.preview-stat-value { font-weight: 600; color: var(--text-primary); }

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

.page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--text-muted);
    margin-bottom: 24px;
}
.page-breadcrumb a { color: var(--accent); text-decoration: none; }
.page-breadcrumb a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="page-breadcrumb">
    <a href="{{ route('tournaments.index') }}">Turnamen Saya</a>
    <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
    <span>Buat Baru</span>
</div>

<form method="POST" action="{{ route('tournaments.store') }}" id="create-form">
    @csrf

    <div class="create-grid">
        <!-- Left: Form -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-trophy" style="color:var(--accent)"></i> &nbsp;Informasi Turnamen</h2>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Turnamen *</label>
                    <input type="text" id="name" name="name" class="form-control"
                           placeholder="cth: Turnamen Tenis Meja HUT RI ke-80"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control"
                              placeholder="Informasi turnamen, lokasi, tanggal, dll...">{{ old('description') }}</textarea>
                </div>

                <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                               value="{{ old('start_date') }}" onchange="updatePreview()">
                        @error('start_date')
                            <div class="form-error" style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                               value="{{ old('end_date') }}" onchange="updatePreview()">
                        @error('end_date')
                            <div class="form-error" style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="divider">

                <div class="card-title" style="margin-bottom:16px">
                    <i class="fa-solid fa-sliders" style="color:var(--accent)"></i> &nbsp;Pengaturan Bracket
                </div>

                <div class="form-group">
                    <label class="form-label">Format Bracket</label>
                    <div class="option-cards">
                        <label class="option-card">
                            <input type="radio" name="type" value="single_elimination" checked onchange="updatePreview()">
                            <i class="fa-solid fa-sitemap" style="color:var(--accent)"></i>
                            <span>Single Elimination</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="type" value="round_robin" onchange="updatePreview()">
                            <i class="fa-solid fa-table-list" style="color:var(--green)"></i>
                            <span>Round Robin</span>
                        </label>
                    </div>
                    <div class="form-hint" id="format-hint">Peserta yang kalah langsung gugur. Format paling umum untuk turnamen.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Opsi Tambahan</label>

                    <div class="toggle-group" style="margin-bottom: 14px" id="third_place_container">
                        <label class="toggle">
                            <input type="checkbox" id="third_place_match" name="third_place_match" value="1"
                                   {{ old('third_place_match') ? 'checked' : '' }}
                                   onchange="updatePreview()">
                            <span class="toggle-slider"></span>
                        </label>
                        <div>
                            <div style="font-size:14px;font-weight:600;color:var(--text-primary)">Pertandingan Perebutan Juara 3</div>
                            <div class="form-hint" style="margin:0">Semifinal losers bermain untuk posisi 3</div>
                        </div>
                    </div>

                    <div class="toggle-group">
                        <label class="toggle">
                            <input type="checkbox" id="seeded" name="seeded" value="1"
                                   {{ old('seeded') ? 'checked' : '' }}
                                   onchange="updatePreview()">
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
                <a href="{{ route('tournaments.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Buat Turnamen & Tambah Peserta
                </button>
            </div>
        </div>

        <!-- Right: Preview -->
        <div class="preview-card">
            <div class="preview-title">📋 Preview Turnamen</div>

            <div class="preview-bracket-icon">🏓</div>

            <div class="preview-stat">
                <span class="preview-stat-label">Format</span>
                <span class="preview-stat-value" id="prev-format">Single Elimination</span>
            </div>
            <div class="preview-stat">
                <span class="preview-stat-label">Tanggal</span>
                <span class="preview-stat-value" id="prev-dates">-</span>
            </div>
            <div class="preview-stat">
                <span class="preview-stat-label">Juara 3</span>
                <span class="preview-stat-value" id="prev-third">Tidak</span>
            </div>
            <div class="preview-stat">
                <span class="preview-stat-label">Seeding</span>
                <span class="preview-stat-value" id="prev-seeded">Tidak (Acak)</span>
            </div>
            <div class="preview-stat">
                <span class="preview-stat-label">Peserta Min.</span>
                <span class="preview-stat-value">2 orang</span>
            </div>

            <div style="margin-top:20px;padding:14px;background:var(--accent-light);border-radius:var(--radius-sm);border:1px solid rgba(59,130,246,0.2)">
                <div style="font-size:13px;color:var(--accent);font-weight:600;margin-bottom:6px">
                    <i class="fa-solid fa-lightbulb"></i> Info
                </div>
                <div style="font-size:12px;color:var(--text-secondary);line-height:1.6">
                    Setelah membuat turnamen, Anda bisa menambahkan peserta dan kemudian men-generate bracket secara otomatis.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function updatePreview() {
    const format = document.querySelector('input[name="type"]:checked').value;
    const thirdPlace = document.getElementById('third_place_match').checked;
    const seeded = document.getElementById('seeded').checked;

    if (format === 'single_elimination') {
        document.getElementById('prev-format').textContent = 'Single Elimination';
        document.getElementById('format-hint').textContent = 'Peserta yang kalah langsung gugur. Format paling umum untuk turnamen.';
        document.getElementById('third_place_container').style.display = 'flex';
        document.getElementById('prev-third').parentElement.style.display = 'flex';
    } else {
        document.getElementById('prev-format').textContent = 'Round Robin';
        document.getElementById('format-hint').textContent = 'Semua peserta akan saling bertanding (Setengah Kompetisi). Menang = 1 Poin.';
        document.getElementById('third_place_container').style.display = 'none';
        document.getElementById('prev-third').parentElement.style.display = 'none';
    }

    document.getElementById('prev-third').textContent = thirdPlace ? 'Ya' : 'Tidak';
    document.getElementById('prev-seeded').textContent = seeded ? 'Ya (Berdasarkan Seed)' : 'Tidak (Acak)';

    // Date range preview
    const startDateVal = document.getElementById('start_date').value;
    const endDateVal = document.getElementById('end_date').value;
    let dateStr = '-';
    
    if (startDateVal || endDateVal) {
        const formatDate = (dateString) => {
            if (!dateString) return '';
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return '';
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${String(d.getDate()).padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
        };

        const startFormatted = formatDate(startDateVal);
        const endFormatted = formatDate(endDateVal);

        if (startFormatted && endFormatted) {
            if (startDateVal === endDateVal) {
                dateStr = startFormatted;
            } else {
                dateStr = `${startFormatted} - ${endFormatted}`;
            }
        } else if (startFormatted) {
            dateStr = startFormatted;
        } else if (endFormatted) {
            dateStr = endFormatted;
        }
    }
    document.getElementById('prev-dates').textContent = dateStr;
}

document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
