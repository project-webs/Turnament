<x-guest-layout>
    <h2 style="font-size:20px;font-weight:800;margin-bottom:6px">Buat Akun Baru</h2>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">Daftar untuk mulai mengelola turnamen</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input id="name" type="text" name="name" class="form-control"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Ahmad Fauzi">
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required autocomplete="username"
                   placeholder="admin@example.com">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control"
                   required autocomplete="new-password" placeholder="Min. 8 karakter">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                   required autocomplete="new-password" placeholder="Ulangi password">
            @error('password_confirmation')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">
            <i class="fa-solid fa-user-plus"></i>
            Daftar
        </button>
    </form>

    <div class="auth-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</x-guest-layout>
