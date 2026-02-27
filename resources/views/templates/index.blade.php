<x-layouts.app :title="'Template Transaksi'">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
            <i class="icon-plus"></i> Buat Template
        </button>
    </x-slot:actions>

    @if($templates->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px;">
            @foreach($templates as $tpl)
                <div class="card card-padding" style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="table-row-icon" style="background: {{ $tpl->color ?? '#0F766E' }}12; color: {{ $tpl->color ?? '#0F766E' }}; flex-shrink: 0;">
                            <i class="icon-{{ $tpl->icon ?? 'zap' }}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 13px; font-weight: 600;">{{ $tpl->name }}</div>
                            <div style="font-size: 11px; color: var(--n-text-muted);">
                                {{ $tpl->category->name ?? '-' }} &middot; {{ $tpl->account->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 16px; font-weight: 700; color: {{ $tpl->type === 'income' ? 'var(--n-income)' : 'var(--n-expense)' }};">
                        {{ $tpl->type === 'income' ? '+' : '-' }}Rp {{ number_format($tpl->amount, 0, ',', '.') }}
                    </div>
                    @if($tpl->description)
                        <div style="font-size: 11.5px; color: var(--n-text-secondary);">{{ $tpl->description }}</div>
                    @endif
                    <div style="display: flex; gap: 6px; margin-top: auto;">
                        <form method="POST" action="{{ route('templates.use', $tpl) }}" style="flex: 1;" data-confirm="Catat transaksi dari template '{{ $tpl->name }}'?">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                <i class="icon-zap"></i> Gunakan
                            </button>
                        </form>
                        <form method="POST" action="{{ route('templates.destroy', $tpl) }}" data-confirm="Hapus template?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm"><i class="icon-trash-2"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <i class="icon-zap"></i>
                <div class="empty-state-title">Belum ada template</div>
                <div class="empty-state-desc">Simpan transaksi favorit untuk pencatatan 1 klik.</div>
                <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                    <i class="icon-plus"></i> Buat Template
                </button>
            </div>
        </div>
    @endif

    {{-- Add Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Buat Template</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" action="{{ route('templates.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Template</label>
                        <input type="text" name="name" class="form-input" placeholder="contoh: Kopi Starbucks" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" id="tplType" class="form-select" required onchange="filterTplCats()">
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
                            <select name="category_id" id="tplCat" class="form-select" required>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="text" name="amount" class="form-input" data-currency placeholder="50.000" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Warna</label>
                            <input type="color" name="color" value="#0F766E" style="width:48px;height:36px;border:1px solid var(--n-border-strong);border-radius:8px;cursor:pointer;padding:2px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="description" class="form-input">
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
        function filterTplCats() {
            const type = document.getElementById('tplType').value;
            const sel = document.getElementById('tplCat');
            let first = null;
            sel.querySelectorAll('option').forEach(o => {
                if (o.dataset.type === type) { o.style.display = ''; if(!first) first = o; } else { o.style.display = 'none'; }
            });
            if (first) sel.value = first.value;
        }
        document.addEventListener('DOMContentLoaded', filterTplCats);
    </script>
    @endpush
</x-layouts.app>
