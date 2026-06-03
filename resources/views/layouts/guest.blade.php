<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg-primary:   #0f1117;
            --bg-card:      #1e2433;
            --border:       #2a3348;
            --accent:       #3b82f6;
            --accent-hover: #2563eb;
            --accent-glow:  rgba(59,130,246,0.25);
            --text-primary: #f1f5f9;
            --text-secondary:#94a3b8;
            --text-muted:   #64748b;
            --red:          #ef4444;
            --red-light:    rgba(239,68,68,0.12);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.08), transparent 60%);
            top: -200px; left: -200px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(168,85,247,0.06), transparent 60%);
            bottom: -150px; right: -150px;
            pointer-events: none;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--accent), #6366f1);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin: 0 auto 14px;
            box-shadow: 0 0 40px var(--accent-glow);
        }
        .auth-brand h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
        }
        .auth-brand p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
        }
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        .form-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 6px;
        }
        .btn {
            display: flex; align-items: center; justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 8px 24px var(--accent-glow);
            transform: translateY(-1px);
        }
        .auth-link {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 20px;
        }
        .auth-link a { color: var(--accent); text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        .checkbox-group input { accent-color: var(--accent); }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-brand">
            <div class="auth-brand-icon">🏓</div>
            <h1>TenisMeja Tournament</h1>
            <p>Platform Manajemen Turnamen Tenis Meja</p>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
