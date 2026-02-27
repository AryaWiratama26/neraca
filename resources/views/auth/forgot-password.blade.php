<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — Neraca</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            <h1 class="auth-title">Lupa password?</h1>
            <p class="auth-subtitle">Masukkan email terdaftar Anda, kami akan mengirimkan link untuk mereset password.</p>

            @if ($errors->any())
                <div class="auth-alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
            </form>
            <p class="auth-footer"><a href="{{ route('login') }}">Kembali ke halaman Masuk</a></p>
        </div>
    </div>
</body>
</html>
