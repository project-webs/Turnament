@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('topbar-actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div style="max-width:600px">
        <div class="card">
            <div class="card-header" style="border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:24px">
                <div class="card-title">Informasi User</div>
            </div>

            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}" placeholder="Contoh: Budi Santoso">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color:var(--red)">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}" placeholder="budi@example.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span style="color:var(--red)">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span style="color:var(--red)">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password">
                </div>

                <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
