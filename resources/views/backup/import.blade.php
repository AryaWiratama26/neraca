<x-layouts.app :title="'Restore Backup'">
    <div style="max-width: 560px;">
        <div class="card card-padding">
            <h2 style="font-size: 14px; font-weight: 600; margin-bottom: 14px;">
                <i class="icon-upload"></i> Restore dari Backup
            </h2>

            <div style="margin-bottom: 16px; padding: 12px; border-radius: 10px; background: rgba(220,38,38,0.04); border: 1px solid rgba(220,38,38,0.1);">
                <div style="font-size: 12px; font-weight: 600; color: #DC2626; margin-bottom: 4px;">⚠️ Perhatian</div>
                <div style="font-size: 11.5px; color: var(--n-text-secondary); line-height: 1.6;">
                    Import akan menambahkan data dari file backup ke akun Anda. Data yang sudah ada <strong>tidak</strong> akan dihapus atau ditimpa. Akun dengan nama yang sama tidak akan diduplikasi.
                </div>
            </div>

            <form method="POST" action="{{ route('backup.import.store') }}" enctype="multipart/form-data" data-confirm="Yakin ingin merestore data dari backup?">
                @csrf
                <div class="form-group">
                    <label class="form-label">Pilih File Backup (.json)</label>
                    <input type="file" name="file" class="form-input" accept=".json" required style="padding: 8px;">
                    @error('file')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                    <i class="icon-upload"></i> Restore Sekarang
                </button>
            </form>
        </div>

        <div class="card card-padding" style="margin-top: 14px;">
            <h2 style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">
                <i class="icon-download"></i> Download Backup
            </h2>
            <p style="font-size: 12px; color: var(--n-text-secondary); margin-bottom: 12px;">
                Unduh seluruh data keuangan Anda dalam format JSON.
            </p>
            <a href="{{ route('backup.export') }}" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">
                <i class="icon-download"></i> Download Full Backup
            </a>
        </div>
    </div>
</x-layouts.app>
