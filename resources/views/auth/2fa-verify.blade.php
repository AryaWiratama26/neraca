<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca — Verifikasi 2FA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="icon-shield"></i></div>
                <h1 class="auth-brand-name">Verifikasi 2FA</h1>
                <p class="auth-brand-tagline">Masukkan kode dari aplikasi authenticator Anda</p>
            </div>

            @if(session('error'))
                <div style="padding: 10px 14px; border-radius: 10px; background: rgba(220,38,38,0.06); border: 1px solid rgba(220,38,38,0.15); color: #DC2626; font-size: 12px; margin-bottom: 16px;">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.code') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Kode OTP (6 digit)</label>
                    <input type="text" name="code" class="form-input" maxlength="6" placeholder="123456" required autofocus style="text-align: center; font-size: 22px; font-weight: 600; letter-spacing: 6px;">
                    @error('code')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                    <i class="icon-check"></i> Verifikasi
                </button>
            </form>

            <div style="margin-top: 16px; text-align: center;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
