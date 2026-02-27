<x-layouts.app :title="'Kategori'">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
            <i class="icon-plus"></i> Tambah Kategori
        </button>
    </x-slot:actions>

    @php
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');
    @endphp

    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <div class="table-header">
                <h2 class="table-title">Pemasukan</h2>
                <span class="badge badge--income">{{ $incomeCategories->count() }}</span>
            </div>
            @forelse($incomeCategories as $cat)
                <div class="table-row">
                    <div class="table-row-icon" style="background: {{ $cat->color ?? '#059669' }}12; color: {{ $cat->color ?? '#059669' }};">
                        <i class="icon-{{ $cat->icon ?? 'circle' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $cat->name }}</div>
                        <div class="table-row-subtitle">{{ $cat->is_default ? 'Bawaan' : 'Custom' }}</div>
                    </div>
                    @unless($cat->is_default)
                        <div class="table-row-actions">
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" data-confirm="Hapus?">
                                @csrf @method('DELETE')
                                <button type="submit" class="delete-btn"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                            </form>
                        </div>
                    @endunless
                </div>
            @empty
                <div class="table-empty">Belum ada kategori pemasukan.</div>
            @endforelse
        </div>

        <div class="card">
            <div class="table-header">
                <h2 class="table-title">Pengeluaran</h2>
                <span class="badge badge--expense">{{ $expenseCategories->count() }}</span>
            </div>
            @forelse($expenseCategories as $cat)
                <div class="table-row">
                    <div class="table-row-icon" style="background: {{ $cat->color ?? '#DC2626' }}12; color: {{ $cat->color ?? '#DC2626' }};">
                        <i class="icon-{{ $cat->icon ?? 'circle' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $cat->name }}</div>
                        <div class="table-row-subtitle">{{ $cat->is_default ? 'Bawaan' : 'Custom' }}</div>
                    </div>
                    @unless($cat->is_default)
                        <div class="table-row-actions">
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" data-confirm="Hapus?">
                                @csrf @method('DELETE')
                                <button type="submit" class="delete-btn"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                            </form>
                        </div>
                    @endunless
                </div>
            @empty
                <div class="table-empty">Belum ada kategori pengeluaran.</div>
            @endforelse
        </div>
    </div>

    {{-- Add Category Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Kategori</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">
                    <i class="icon-x" style="font-size:16px;"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-input" placeholder="contoh: Side Hustle" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select name="type" class="form-select" required>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Warna</label>
                            <input type="color" name="color" value="#14B8A6" style="width:48px;height:36px;border:1px solid var(--n-border-strong);border-radius:8px;cursor:pointer;padding:2px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
