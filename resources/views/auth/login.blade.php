<x-guest-layout>
    <h2 style="font-size:20px;font-weight:800;margin-bottom:6px">Selamat Datang!</h2>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">Masuk ke akun Anda untuk melanjutkan</p>

    <!-- Session Status -->
    @if (session('status'))
        <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px;padding:12px 16px;font-size:14px;color:#22c55e;margin-bottom:20px">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="admin@example.com">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <label for="password" class="form-label" style="margin:0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:12px;color:var(--accent);text-decoration:none">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" class="form-control"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="checkbox-group" style="margin-bottom:24px">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Ingat saya</label>
        </div>

        <button type="submit" class="btn">
            <i class="fa-solid fa-right-to-bracket"></i>
            Masuk
        </button>
    </form>

    @if (Route::has('register'))
    <div class="auth-link">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>
    @endif
</x-guest-layout>
