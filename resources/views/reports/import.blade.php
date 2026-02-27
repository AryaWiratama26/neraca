<x-layouts.app :title="'Import Data'">

    <div style="max-width: 560px;">
        <div class="card card-padding">
            <h2 style="font-size: 14px; font-weight: 600; margin-bottom: 14px;">
                <i class="icon-upload"></i> Import Transaksi dari CSV/Excel
            </h2>

            <div style="margin-bottom: 16px; padding: 12px; border-radius: 10px; background: rgba(15,118,110,0.06); border: 1px solid rgba(15,118,110,0.1);">
                <div style="font-size: 12px; font-weight: 600; color: var(--n-primary); margin-bottom: 6px;">Format File</div>
                <div style="font-size: 11.5px; color: var(--n-text-secondary); line-height: 1.7;">
                    File harus memiliki header kolom:<br>
                    <code style="font-size: 11px; background: rgba(0,0,0,0.04); padding: 2px 6px; border-radius: 4px;">Tanggal | Tipe | Kategori | Akun | Jumlah | Catatan | Tag</code>
                    <br><br>
                    <strong>Tanggal:</strong> dd/mm/yyyy atau yyyy-mm-dd<br>
                    <strong>Tipe:</strong> Pemasukan atau Pengeluaran<br>
                    <strong>Akun:</strong> Nama akun yang sudah ada<br>
                    <strong>Kategori:</strong> Nama kategori yang sudah ada<br>
                    <strong>Tag:</strong> Dipisahkan koma (opsional)
                </div>
            </div>

            <form method="POST" action="{{ route('reports.import.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Pilih File</label>
                    <input type="file" name="file" class="form-input" accept=".csv,.xlsx,.xls" required style="padding: 8px;">
                    @error('file')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                    <i class="icon-upload"></i> Import Sekarang
                </button>
            </form>
        </div>

        @if(session('import_errors'))
            <div class="card card-padding" style="margin-top: 14px;">
                <h2 style="font-size: 13px; font-weight: 600; color: var(--n-expense); margin-bottom: 8px;">
                    <i class="icon-alert-circle"></i> Error saat import:
                </h2>
                <ul style="font-size: 11.5px; color: var(--n-text-secondary); padding-left: 18px;">
                    @foreach(session('import_errors') as $err)
                        <li style="margin-bottom: 3px;">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-layouts.app>
