@extends('layouts.app')

@section('title', 'Tambah Pertandingan Persahabatan')
@section('page-title', 'Tambah Pertandingan Persahabatan')

@push('styles')
<style>
.form-card {
    max-width: 600px;
    margin: 0 auto;
}

.score-inputs {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 8px;
}

.score-inputs input {
    width: 80px;
    text-align: center;
    font-size: 20px;
    font-weight: 700;
}

.score-divider {
    font-size: 24px;
    font-weight: 800;
    color: var(--text-muted);
}
</style>
@endpush

@section('content')
<div class="form-card card">
    <div class="card-header" style="margin-bottom: 24px;">
        <h2 class="card-title">Data Pertandingan</h2>
        <a href="{{ route('friendly-matches.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('friendly-matches.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="ptm_name" class="form-label">Nama PTM</label>
            <input type="text" name="ptm_name" id="ptm_name" class="form-control" value="{{ old('ptm_name') }}" required placeholder="Contoh: PTM Bintang Mas">
            @error('ptm_name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="match_date" class="form-label">Tanggal Pertemuan</label>
            <input type="date" name="match_date" id="match_date" class="form-control" value="{{ old('match_date', date('Y-m-d')) }}" required>
            @error('match_date') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Keterangan (Opsional)</label>
            <textarea name="notes" id="notes" class="form-control" placeholder="Contoh: Uji coba tim inti">{{ old('notes') }}</textarea>
            @error('notes') <div class="form-error">{{ $message }}</div> @enderror
        </div>



        <div class="divider"></div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="reset" class="btn btn-secondary">Reset</button>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Pertandingan
            </button>
        </div>
    </form>
</div>
@endsection
