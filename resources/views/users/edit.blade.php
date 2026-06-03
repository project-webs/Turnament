@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

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

            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $user->name) }}" placeholder="Contoh: Budi Santoso">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color:var(--red)">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $user->email) }}" placeholder="budi@example.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="divider"></div>
                <div class="card-title" style="margin-bottom:16px;font-size:14px">Ganti Password (Opsional)</div>

                <div class="form-group">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengganti password">
                    <div class="form-hint">Minimal 8 karakter.</div>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                </div>

                <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Perbarui User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
