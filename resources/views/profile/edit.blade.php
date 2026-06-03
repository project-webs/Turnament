@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div style="max-width:640px">

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            <i class="fa-solid fa-check"></i> Profil berhasil diperbarui.
        </div>
    @endif

    <!-- Update Profile -->
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-user" style="color:var(--accent)"></i> &nbsp;Informasi Profil</h2>
        </div>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PATCH')

            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
        </form>
    </div>

    <!-- Update Password -->
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-lock" style="color:var(--yellow)"></i> &nbsp;Ubah Password</h2>
        </div>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control">
                @error('current_password','updatePassword') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control">
                @error('password','updatePassword') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-key"></i> Ubah Password
            </button>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="card" style="border-color:rgba(239,68,68,0.3)">
        <div class="card-header">
            <h2 class="card-title" style="color:var(--red)"><i class="fa-solid fa-triangle-exclamation"></i> &nbsp;Hapus Akun</h2>
        </div>
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px">Tindakan ini permanen dan tidak dapat dibatalkan. Semua data turnamen akan ikut terhapus.</p>

        <button onclick="openModal('delete-modal')" class="btn btn-danger">
            <i class="fa-solid fa-trash"></i> Hapus Akun Saya
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" style="color:var(--red)">⚠️ Konfirmasi Hapus Akun</div>
            <button class="modal-close" onclick="closeModal('delete-modal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')
            <div class="modal-body">
                <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px">
                    Masukkan password Anda untuk mengkonfirmasi penghapusan akun.
                </p>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                    @error('password','userDeletion') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('delete-modal')">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection
