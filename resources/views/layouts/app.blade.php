<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TenisMeja Tournament</title>
    <meta name="description" content="@yield('meta_description', 'Platform manajemen turnamen tenis meja — buat bracket, kelola peserta, dan catat hasil pertandingan.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary:    #0f1117;
            --bg-secondary:  #161b27;
            --bg-card:       #1e2433;
            --bg-card-hover: #252c3d;
            --border:        #2a3348;
            --border-light:  #3a4560;

            --accent:        #3b82f6;
            --accent-hover:  #2563eb;
            --accent-glow:   rgba(59, 130, 246, 0.25);
            --accent-light:  rgba(59, 130, 246, 0.12);

            --green:         #22c55e;
            --green-light:   rgba(34, 197, 94, 0.15);
            --yellow:        #f59e0b;
            --yellow-light:  rgba(245, 158, 11, 0.15);
            --red:           #ef4444;
            --red-light:     rgba(239, 68, 68, 0.15);
            --purple:        #a855f7;
            --purple-light:  rgba(168, 85, 247, 0.15);

            --text-primary:  #f1f5f9;
            --text-secondary:#94a3b8;
            --text-muted:    #64748b;

            --sidebar-w:     260px;
            --header-h:      64px;
            --radius:        12px;
            --radius-sm:     8px;
            --shadow:        0 4px 24px rgba(0,0,0,0.4);
            --transition:    0.2s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform var(--transition);
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #6366f1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .sidebar-brand-text {
            line-height: 1.2;
        }
        .sidebar-brand-text span:first-child {
            display: block;
            font-weight: 700;
            font-size: 15px;
            color: var(--text-primary);
        }
        .sidebar-brand-text span:last-child {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--text-muted);
            text-transform: uppercase;
            padding: 8px 12px 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .nav-link.active {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid rgba(59,130,246,0.2);
        }

        .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-email {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: color var(--transition);
            font-size: 14px;
        }
        .logout-btn:hover { color: var(--red); }

        /* ── MAIN CONTENT ────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: var(--header-h);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: var(--bg-secondary);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .main-content {
            flex: 1;
            padding: 28px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── ALERTS ──────────────────────────────────────────── */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity:0; transform:translateY(-10px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .alert-success { background: var(--green-light); color: var(--green); border: 1px solid rgba(34,197,94,0.3); }
        .alert-error   { background: var(--red-light);   color: var(--red);   border: 1px solid rgba(239,68,68,0.3); }
        .alert-info    { background: var(--accent-light); color: var(--accent); border: 1px solid rgba(59,130,246,0.3); }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--accent-glow);
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--bg-card-hover); }
        .btn-danger {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid rgba(239,68,68,0.3);
        }
        .btn-danger:hover { background: var(--red); color: #fff; }
        .btn-success {
            background: var(--green);
            color: #fff;
        }
        .btn-success:hover { background: #16a34a; }
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        .btn-icon { padding: 8px 10px; }

        /* ── CARDS ───────────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ── FORMS ───────────────────────────────────────────── */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color var(--transition);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .form-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }
        .form-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 6px;
        }

        .toggle-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toggle {
            position: relative;
            width: 44px;
            height: 24px;
        }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: var(--border);
            border-radius: 100px;
            cursor: pointer;
            transition: var(--transition);
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 18px; height: 18px;
            left: 3px; top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: var(--transition);
        }
        .toggle input:checked + .toggle-slider { background: var(--accent); }
        .toggle input:checked + .toggle-slider::before { transform: translateX(20px); }

        /* ── BADGES ──────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-gray   { background: rgba(100,116,139,0.2); color: var(--text-secondary); }
        .badge-green  { background: var(--green-light);   color: var(--green); }
        .badge-blue   { background: var(--accent-light);  color: var(--accent); }
        .badge-yellow { background: var(--yellow-light);  color: var(--yellow); }

        /* ── MODAL ───────────────────────────────────────────── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition);
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .modal {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            width: 100%;
            max-width: 480px;
            transform: scale(0.95) translateY(10px);
            transition: transform 0.2s ease;
            box-shadow: var(--shadow);
        }
        .modal-overlay.active .modal { transform: scale(1) translateY(0); }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }
        .modal-title { font-size: 16px; font-weight: 700; }
        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 18px;
            padding: 4px;
            transition: color var(--transition);
        }
        .modal-close:hover { color: var(--text-primary); }
        .modal-body { padding: 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* ── DIVIDER ─────────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        /* ── EMPTY STATE ─────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.4;
        }
        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .empty-state p { font-size: 14px; margin-bottom: 20px; }

        /* ── RESPONSIVE ──────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-wrapper { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .main-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">🏓</div>
        <div class="sidebar-brand-text">
            <span>TenisMeja</span>
            <span>TOURNAMENT MANAGER</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="{{ route('tournaments.index') }}"
           class="nav-link {{ request()->routeIs('tournaments.index') ? 'active' : '' }}">
            <i class="fa-solid fa-trophy"></i>
            Turnamen Saya
        </a>
        <a href="{{ route('friendly-matches.index') }}"
           class="nav-link {{ request()->routeIs('friendly-matches.*') ? 'active' : '' }}">
            <i class="fa-solid fa-handshake"></i>
            Pertandingan Persahabatan
        </a>
        <a href="{{ route('tournaments.create') }}"
           class="nav-link {{ request()->routeIs('tournaments.create') ? 'active' : '' }}">
            <i class="fa-solid fa-plus-circle"></i>
            Buat Turnamen
        </a>

        <div class="nav-section-label" style="margin-top:12px">Master Data</div>
        <a href="{{ route('players.index') }}"
           class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            Data Peserta
        </a>
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i>
            Manajemen User
        </a>

        <div class="nav-section-label" style="margin-top:12px">Akun</div>
        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="fa-solid fa-user-circle"></i>
            Profil Saya
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-email">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main wrapper -->
<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-right">
            @yield('topbar-actions')
        </div>
    </header>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-xmark"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
// Auto-dismiss alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.4s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 400);
    }, 5000);
});

// Modal handling
function openModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) overlay.classList.add('active');
}
function closeModal(id) {
    const overlay = document.getElementById(id);
    if (overlay) overlay.classList.remove('active');
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('active');
    });
});
</script>

@stack('scripts')
</body>
</html>
