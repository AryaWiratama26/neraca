<x-layouts.app :title="'Transaksi Berulang'">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
            <i class="icon-plus"></i> Tambah Berulang
        </button>
    </x-slot:actions>

    @if($recurring->count() > 0)
        <div class="card">
            @foreach($recurring as $rec)
                <div class="table-row" style="opacity: {{ $rec->is_active ? 1 : 0.5 }};">
                    <div class="table-row-icon" style="background: {{ $rec->category->color ?? '#6B7280' }}12; color: {{ $rec->category->color ?? '#6B7280' }};">
                        <i class="icon-{{ $rec->type === 'income' ? 'arrow-up-right' : 'arrow-down-left' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $rec->category->name ?? '-' }}</div>
                        <div class="table-row-subtitle">
                            {{ $rec->account->name ?? '-' }} &middot; {{ $rec->frequency_label }}
                            &middot; Berikutnya: {{ $rec->next_due->format('d M Y') }}
                            @if($rec->description) &middot; {{ $rec->description }} @endif
                        </div>
                    </div>
                    <div class="table-row-amount {{ $rec->type === 'income' ? 'table-row-amount--income' : 'table-row-amount--expense' }}">
                        {{ $rec->type === 'income' ? '+' : '-' }}Rp {{ number_format($rec->amount, 0, ',', '.') }}
                    </div>
                    <div class="table-row-actions">
                        <form method="POST" action="{{ route('recurring.toggle', $rec) }}" style="margin:0;">
                            @csrf
                            <button type="submit" title="{{ $rec->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" style="padding:4px;border:none;background:none;cursor:pointer;color:var(--n-text-muted);">
                                <i class="icon-{{ $rec->is_active ? 'pause' : 'play' }}" style="font-size:14px;"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('recurring.destroy', $rec) }}" data-confirm="Hapus?" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="delete-btn"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <i class="icon-repeat"></i>
                <div class="empty-state-title">Belum ada transaksi berulang</div>
                <div class="empty-state-desc">Otomatiskan gaji, cicilan, dan langganan bulanan.</div>
                <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                    <i class="icon-plus"></i> Tambah Berulang
                </button>
            </div>
        </div>
    @endif

    {{-- Add Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Transaksi Berulang</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" action="{{ route('recurring.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" id="recType" class="form-select" required onchange="filterCats()">
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Akun</label>
                            <select name="account_id" class="form-select" required>
                                @foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" id="recCat" class="form-select" required>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="text" name="amount" class="form-input" data-currency placeholder="1.000.000" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Frekuensi</label>
                            <select name="frequency" class="form-select" required>
                                <option value="monthly">Bulanan</option>
                                <option value="weekly">Mingguan</option>
                                <option value="daily">Harian</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sampai (opsional)</label>
                            <input type="date" name="end_date" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="description" class="form-input" placeholder="Gaji, Netflix, Cicilan...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterCats() {
            const type = document.getElementById('recType').value;
            const sel = document.getElementById('recCat');
            let first = null;
            sel.querySelectorAll('option').forEach(o => {
                if (o.dataset.type === type) { o.style.display = ''; if(!first) first = o; } else { o.style.display = 'none'; }
            });
            if (first) sel.value = first.value;
        }
        document.addEventListener('DOMContentLoaded', filterCats);
    </script>
    @endpush
</x-layouts.app>
