<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — Neraca</title>
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
            <h1 class="auth-title">Reset password Anda</h1>
            <p class="auth-subtitle">Silakan buat password baru untuk akun Anda.</p>

            @if (session('status'))
                <div class="auth-alert" style="background: rgba(5,150,105,0.1); color: #059669;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $email) }}" required readonly style="background: var(--n-bg-secondary); cursor: not-allowed; opacity: 0.8;">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Buat password yang kuat" required style="padding-right: 40px;" autofocus>
                        <button type="button" onclick="toggleAuthPassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--n-text-muted); cursor: pointer; padding: 4px; display: flex;">
                            <i class="icon-eye" style="font-size: 16px;"></i>
                        </button>
                    </div>
                    <div style="font-size: 12px; color: var(--n-text-muted); margin-top: 6px;">
                        Min. 8 karakter, kombinasi huruf besar, huruf kecil, angka & simbol.
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password" required style="padding-right: 40px;">
                        <button type="button" onclick="toggleAuthPassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--n-text-muted); cursor: pointer; padding: 4px; display: flex;">
                            <i class="icon-eye" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            </form>
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
