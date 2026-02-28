<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar — Neraca</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card" style="position: relative;">
            <a href="{{ url('/') }}" style="position: absolute; top: 24px; left: 24px; display: inline-flex; items: center; gap: 6px; color: var(--n-text-muted); text-decoration: none; font-size: 13px; font-weight: 500; transition: color 0.2s ease;">
                <i class="icon-arrow-left" style="font-size: 14px;"></i> Kembali
            </a>
            <div class="auth-brand" style="margin-top: 16px;">
                <div class="auth-brand-icon"><i class="icon-wallet"></i></div>
                <span class="auth-brand-name">Neraca</span>
            </div>
            <h1 class="auth-title">Buat akun baru</h1>
            <p class="auth-subtitle">Mulai kelola keuangan Anda dengan lebih baik</p>

            @if ($errors->any())
                <div class="auth-alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Nama lengkap</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required style="padding-right: 40px;">
                        <button type="button" onclick="toggleAuthPassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--n-text-muted); cursor: pointer; padding: 4px; display: flex;">
                            <i class="icon-eye" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi password</label>
                    <div style="position: relative;">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password" required style="padding-right: 40px;">
                        <button type="button" onclick="toggleAuthPassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--n-text-muted); cursor: pointer; padding: 4px; display: flex;">
                            <i class="icon-eye" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Daftar</button>
            </form>
            <p class="auth-footer">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
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
