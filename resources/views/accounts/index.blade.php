<x-layouts.app :title="'Akun'">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
            <i class="icon-plus"></i> Tambah Akun
        </button>
    </x-slot:actions>

    @if($accounts->count() > 0)
        <div class="stats-grid" style="margin-bottom: 20px;">
            <div class="stat-card">
                <div class="stat-icon stat-icon--balance"><i class="icon-wallet"></i></div>
                <div class="stat-label">Total Saldo</div>
                <div class="stat-value">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="card">
            @foreach($accounts as $acc)
                <div class="table-row">
                    <div class="table-row-icon" style="background: {{ $acc->color ?? '#0F766E' }}12; color: {{ $acc->color ?? '#0F766E' }};">
                        <i class="icon-{{ $acc->icon ?? 'wallet' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $acc->name }}</div>
                        <div class="table-row-subtitle">{{ $acc->type_label }}</div>
                    </div>
                    <div class="table-row-amount" style="color: var(--n-text);">
                        Rp {{ number_format($acc->balance, 0, ',', '.') }}
                    </div>
                    <div class="table-row-actions">
                        <button onclick="openEditAccount({{ json_encode([
                            'id' => $acc->id,
                            'name' => $acc->name,
                            'type' => $acc->type,
                            'balance' => $acc->balance,
                            'color' => $acc->color ?? '#0F766E',
                        ]) }})" title="Edit" style="padding:4px;border:none;background:none;cursor:pointer;color:var(--n-text-muted);border-radius:6px;display:flex;">
                            <i class="icon-pencil" style="font-size:14px;"></i>
                        </button>
                        <form method="POST" action="{{ route('accounts.destroy', $acc) }}" data-confirm="Hapus akun ini? Semua transaksi terkait akan tetap ada." style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="delete-btn" title="Hapus"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <i class="icon-credit-card"></i>
                <div class="empty-state-title">Belum ada akun</div>
                <div class="empty-state-desc">Tambahkan akun seperti dompet, rekening bank, atau e-wallet.</div>
                <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                    <i class="icon-plus"></i> Tambah Akun Pertama
                </button>
            </div>
        </div>
    @endif

    {{-- Add Account Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Akun</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" action="{{ route('accounts.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Akun</label>
                        <input type="text" name="name" class="form-input" placeholder="contoh: BCA, GoPay, Dompet" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select name="type" class="form-select" required>
                                <option value="cash">Uang Tunai</option>
                                <option value="bank">Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="savings">Tabungan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Saldo Awal</label>
                            <input type="text" name="balance" class="form-input" value="0" data-currency placeholder="1.000.000" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna</label>
                        <input type="color" name="color" value="#0F766E" style="width:48px;height:36px;border:1px solid var(--n-border-strong);border-radius:8px;cursor:pointer;padding:2px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Account Modal --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Edit Akun</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Akun</label>
                        <input type="text" name="name" id="editName" class="form-input" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select name="type" id="editType" class="form-select" required>
                                <option value="cash">Uang Tunai</option>
                                <option value="bank">Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="savings">Tabungan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Saldo</label>
                            <input type="text" name="balance" id="editBalance" class="form-input" data-currency required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna</label>
                        <input type="color" name="color" id="editColor" style="width:48px;height:36px;border:1px solid var(--n-border-strong);border-radius:8px;cursor:pointer;padding:2px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditAccount(acc) {
            document.getElementById('editForm').action = '/accounts/' + acc.id;
            document.getElementById('editName').value = acc.name;
            document.getElementById('editType').value = acc.type;
            document.getElementById('editBalance').value = formatCurrency(String(Math.round(acc.balance)));
            document.getElementById('editColor').value = acc.color;
            document.getElementById('editModal').classList.add('open');
        }
    </script>
    @endpush
</x-layouts.app>
