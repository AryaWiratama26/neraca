<x-layouts.app :title="'Transaksi'">
    <x-slot:actions>
        @if($accounts->count() > 0)
            <div style="display: flex; gap: 6px;">
                <button class="btn btn-secondary btn-sm" onclick="document.getElementById('transferModal').classList.add('open')">
                    <i class="icon-arrow-left-right"></i> Transfer
                </button>
                <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
                    <i class="icon-plus"></i> Catat Transaksi
                </button>
            </div>
        @endif
    </x-slot:actions>

    @if($accounts->count() === 0)
        <div class="card">
            <div class="empty-state">
                <i class="icon-credit-card"></i>
                <div class="empty-state-title">Buat akun terlebih dahulu</div>
                <div class="empty-state-desc">Anda perlu menambahkan akun sebelum bisa mencatat transaksi.</div>
                <a href="{{ route('accounts.index') }}" class="btn btn-primary btn-sm">
                    <i class="icon-plus"></i> Tambah Akun
                </a>
            </div>
        </div>
    @else
        {{-- Filters --}}
        <form method="GET" action="{{ route('transactions.index') }}" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px;">
            <select name="type" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
            <input type="text" name="tag" class="form-input" style="width: auto; min-width: 120px;" value="{{ request('tag') }}" placeholder="Filter tag...">
            <input type="date" name="date_from" class="form-input" style="width: auto;" value="{{ request('date_from') }}" placeholder="Dari" onchange="this.form.submit()">
            <input type="date" name="date_to" class="form-input" style="width: auto;" value="{{ request('date_to') }}" placeholder="Sampai" onchange="this.form.submit()">
            <button type="submit" class="btn btn-secondary btn-sm"><i class="icon-search"></i></button>
            @if(request()->hasAny(['type', 'date_from', 'date_to', 'tag']))
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm"><i class="icon-x"></i> Reset</a>
            @endif
        </form>

        <div class="card">
            @forelse($transactions as $tx)
                <div class="table-row">
                    <div class="table-row-icon" style="background: {{ $tx->category->color ?? '#6B7280' }}12; color: {{ $tx->category->color ?? '#6B7280' }};">
                        <i class="icon-{{ $tx->type === 'income' ? 'arrow-up-right' : 'arrow-down-left' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $tx->category->name ?? 'Transfer' }}</div>
                        <div class="table-row-subtitle">
                            {{ $tx->account->name ?? '-' }} &middot; {{ $tx->date->format('d M Y') }}
                            @if($tx->description) &middot; {{ Str::limit($tx->description, 40) }} @endif
                        </div>
                        @if($tx->tags && count($tx->tags) > 0)
                            <div style="margin-top: 3px;">
                                @foreach($tx->tags as $tag)
                                    <span class="tag-badge">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="table-row-amount {{ $tx->type === 'income' ? 'table-row-amount--income' : 'table-row-amount--expense' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </div>
                    <div class="table-row-actions">
                        <button onclick="openEditModal({{ json_encode([
                            'id' => $tx->id,
                            'account_id' => $tx->account_id,
                            'category_id' => $tx->category_id,
                            'type' => $tx->type,
                            'amount' => $tx->amount,
                            'description' => $tx->description,
                            'date' => $tx->date->format('Y-m-d'),
                            'tags' => $tx->tags,
                        ]) }})" title="Edit" style="padding:4px;border:none;background:none;cursor:pointer;color:var(--n-text-muted);border-radius:6px;display:flex;">
                            <i class="icon-pencil" style="font-size:14px;"></i>
                        </button>
                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" data-confirm="Hapus transaksi ini?" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="delete-btn" title="Hapus"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="icon-receipt"></i>
                    <div class="empty-state-title">Belum ada transaksi</div>
                    <div class="empty-state-desc">Mulai catat pemasukan dan pengeluaran Anda.</div>
                    <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                        <i class="icon-plus"></i> Catat Transaksi Pertama
                    </button>
                </div>
            @endforelse
        </div>

        @if($transactions->hasPages())
            <div style="margin-top: 16px; display: flex; justify-content: center;">
                {{ $transactions->links() }}
            </div>
        @endif

        {{-- Add Transaction Modal --}}
        <div class="modal-overlay" id="addModal">
            <div class="modal">
                <div class="modal-header">
                    <h2 class="modal-title">Catat Transaksi</h2>
                    <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
                </div>
                <form method="POST" action="{{ route('transactions.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select name="type" id="txType" class="form-select" required onchange="filterCategories('txType','txCategory')">
                                <option value="expense">Pengeluaran</option>
                                <option value="income">Pemasukan</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Akun</label>
                                <select name="account_id" class="form-select" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" id="txCategory" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Jumlah (Rp)</label>
                                <input type="text" name="amount" class="form-input" placeholder="50.000" data-currency required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catatan (opsional)</label>
                            <input type="text" name="description" class="form-input" placeholder="contoh: Makan siang di kantin">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tag (opsional, pisahkan koma)</label>
                            <input type="text" name="tags" class="form-input" placeholder="makanan, kantor">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Transaction Modal --}}
        <div class="modal-overlay" id="editModal">
            <div class="modal">
                <div class="modal-header">
                    <h2 class="modal-title">Edit Transaksi</h2>
                    <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
                </div>
                <form method="POST" id="editForm">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select name="type" id="editType" class="form-select" required onchange="filterCategories('editType','editCategory')">
                                <option value="expense">Pengeluaran</option>
                                <option value="income">Pemasukan</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Akun</label>
                                <select name="account_id" id="editAccount" class="form-select" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" id="editCategory" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Jumlah (Rp)</label>
                                <input type="text" name="amount" id="editAmount" class="form-input" data-currency required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="editDate" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="description" id="editDescription" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tag (opsional, pisahkan koma)</label>
                            <input type="text" name="tags" id="editTags" class="form-input" placeholder="makanan, kantor">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Transfer Modal --}}
        <div class="modal-overlay" id="transferModal">
            <div class="modal">
                <div class="modal-header">
                    <h2 class="modal-title">Transfer Antar Akun</h2>
                    <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
                </div>
                <form method="POST" action="{{ route('transactions.transfer') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Dari Akun</label>
                                <select name="from_account_id" class="form-select" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ke Akun</label>
                                <select name="to_account_id" class="form-select" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Jumlah (Rp)</label>
                                <input type="text" name="amount" class="form-input" placeholder="100.000" data-currency required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catatan (opsional)</label>
                            <input type="text" name="description" class="form-input" placeholder="contoh: Top-up e-wallet">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Transfer</button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            function filterCategories(typeId, catId) {
                const type = document.getElementById(typeId).value;
                const select = document.getElementById(catId);
                const options = select.querySelectorAll('option');
                let first = null;
                options.forEach(opt => {
                    if (opt.dataset.type === type) {
                        opt.style.display = '';
                        if (!first) first = opt;
                    } else {
                        opt.style.display = 'none';
                    }
                });
                if (first) select.value = first.value;
            }

            function openEditModal(tx) {
                document.getElementById('editForm').action = '/transactions/' + tx.id;
                document.getElementById('editType').value = tx.type;
                document.getElementById('editAccount').value = tx.account_id;
                document.getElementById('editAmount').value = formatCurrency(String(Math.round(tx.amount)));
                document.getElementById('editDate').value = tx.date;
                document.getElementById('editDescription').value = tx.description || '';
                document.getElementById('editTags').value = (tx.tags && tx.tags.length) ? tx.tags.join(', ') : '';
                filterCategories('editType', 'editCategory');
                setTimeout(() => {
                    document.getElementById('editCategory').value = tx.category_id;
                }, 50);
                document.getElementById('editModal').classList.add('open');
            }

            document.addEventListener('DOMContentLoaded', () => filterCategories('txType','txCategory'));
        </script>
        @endpush
    @endif
</x-layouts.app>
