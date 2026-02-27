<x-layouts.app :title="'Setup 2FA'">
    <div style="max-width: 480px;">
        <div class="card card-padding">
            <h2 style="font-size: 15px; font-weight: 600; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="icon-shield"></i> Two-Factor Authentication
            </h2>

            @if($user->google2fa_enabled)
                <div style="padding: 12px; border-radius: 10px; background: rgba(5,150,105,0.06); border: 1px solid rgba(5,150,105,0.15); margin-bottom: 16px;">
                    <div style="font-size: 13px; font-weight: 600; color: #059669;">✓ 2FA Aktif</div>
                    <div style="font-size: 12px; color: var(--n-text-secondary); margin-top: 2px;">
                        Akun Anda dilindungi dengan two-factor authentication.
                    </div>
                </div>

                <form method="POST" action="{{ route('2fa.disable') }}" data-confirm="Yakin ingin menonaktifkan 2FA?">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Password untuk menonaktifkan 2FA</label>
                        <input type="password" name="password" class="form-input" required placeholder="Masukkan password Anda">
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">
                        Nonaktifkan 2FA
                    </button>
                </form>
            @else
                <div style="font-size: 12.5px; color: var(--n-text-secondary); margin-bottom: 16px; line-height: 1.6;">
                    Scan QR code di bawah dengan aplikasi authenticator (Google Authenticator, Authy, dll), lalu masukkan kode 6 digit untuk verifikasi.
                </div>

                <div style="display: flex; justify-content: center; margin-bottom: 16px; padding: 16px; background: #fff; border-radius: 12px; border: 1px solid var(--n-border);">
                    {!! $qrCode !!}
                </div>

                <div style="margin-bottom: 16px; padding: 10px; background: var(--n-bg-secondary); border-radius: 8px; text-align: center;">
                    <div style="font-size: 10px; color: var(--n-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Secret Key (manual)</div>
                    <div style="font-size: 14px; font-weight: 600; font-family: monospace; letter-spacing: 2px; margin-top: 4px;">{{ $secret }}</div>
                </div>

                <form method="POST" action="{{ route('2fa.enable') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Kode OTP (6 digit)</label>
                        <input type="text" name="code" class="form-input" maxlength="6" placeholder="123456" required style="text-align: center; font-size: 18px; font-weight: 600; letter-spacing: 4px;">
                        @error('code')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        <i class="icon-shield"></i> Aktifkan 2FA
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.app>
