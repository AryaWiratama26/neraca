<x-layouts.app :title="'Profil'">

    <div style="max-width: 560px;">
        {{-- Profile Info --}}
        <div class="card card-padding" style="margin-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
                <div class="sidebar-avatar" style="width: 48px; height: 48px; font-size: 18px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 600;">{{ $user->name }}</div>
                    <div style="font-size: 13px; color: var(--n-text-muted);">{{ $user->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Uang</label>
                    <select name="currency" class="form-select">
                        <option value="IDR" {{ $user->currency === 'IDR' ? 'selected' : '' }}>IDR — Rupiah Indonesia</option>
                        <option value="USD" {{ $user->currency === 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                        <option value="EUR" {{ $user->currency === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                        <option value="SGD" {{ $user->currency === 'SGD' ? 'selected' : '' }}>SGD — Singapore Dollar</option>
                        <option value="MYR" {{ $user->currency === 'MYR' ? 'selected' : '' }}>MYR — Malaysian Ringgit</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="icon-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card card-padding">
            <h2 style="font-size: 14px; font-weight: 600; margin-bottom: 14px;">Ganti Password</h2>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-input" placeholder="Buat password yang kuat" required>
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                        <div style="font-size: 11px; color: var(--n-text-muted); margin-top: 6px;">Min. 8 karakter, huruf besar/kecil, angka & simbol.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-input" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="icon-lock"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
