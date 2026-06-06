@extends('layouts.app')

@section('title', 'Tambah Peserta')
@section('page-title', 'Tambah Peserta Baru')

@section('topbar-actions')
    <a href="{{ route('players.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div style="max-width:600px">
        <div class="card">
            <div class="card-header" style="border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:24px">
                <div class="card-title">Detail Peserta</div>
            </div>

            <form method="POST" action="{{ route('players.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}" placeholder="Contoh: Budi Santoso">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="nik" class="form-label">No. KTP (NIK)</label>
                    <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" placeholder="Contoh: 3171234567890001">
                    @error('nik') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat Lengkap</label>
                    <textarea name="address" id="address" class="form-control" placeholder="Contoh: Jl. Sudirman No. 1, Jakarta">{{ old('address') }}</textarea>
                    @error('address') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="division" class="form-label">Divisi (Opsional)</label>
                    <input type="text" name="division" id="division" class="form-control" value="{{ old('division') }}" placeholder="Contoh: Divisi 1, Umum, U-19">
                    <div class="form-hint">Digunakan untuk mengkategorikan kemampuan atau kelompok umur pemain.</div>
                    @error('division') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="itr_rating" class="form-label">ITR Rating (Opsional)</label>
                    <input type="number" name="itr_rating" id="itr_rating" class="form-control" value="{{ old('itr_rating', 0) }}" min="0">
                    <div class="form-hint">Sistem rating ITR (mirip USATT). Biarkan 0 jika belum memiliki rating.</div>
                    @error('itr_rating') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                    <a href="{{ route('players.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Simpan Peserta
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
