<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Neraca</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="icon-wallet"></i></div>
                <span class="auth-brand-name">Neraca</span>
            </div>
            <h1 class="auth-title">Selamat datang kembali</h1>
            <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

            @if ($errors->any())
                <div class="auth-alert">
                    @foreach ($errors->all() as $error){{ $error }}@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                        <a href="{{ route('password.request') }}" style="font-size: 11px; color: var(--n-primary); text-decoration: none; font-weight: 500;">Lupa password?</a>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required style="padding-right: 40px;">
                        <button type="button" onclick="toggleAuthPassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--n-text-muted); cursor: pointer; padding: 4px; display: flex;">
                            <i class="icon-eye" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ingat saya
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Masuk</button>
            </form>
            <p class="auth-footer">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </div>
    </div>

    <script>
        function toggleAuthPassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="icon-eye-off" style="font-size: 16px;"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="icon-eye" style="font-size: 16px;"></i>';
            }
        }
    </script>
</body>
</html>
