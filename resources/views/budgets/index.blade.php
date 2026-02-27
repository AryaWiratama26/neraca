<x-layouts.app :title="'Anggaran'">
    <x-slot:actions>
        @if($availableCategories->count() > 0)
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
                <i class="icon-plus"></i> Tambah Anggaran
            </button>
        @endif
    </x-slot:actions>

    @if($budgets->count() > 0)
        <div style="margin-bottom: 14px; font-size: 13px; color: var(--n-text-secondary);">
            Periode: <strong>{{ $now->translatedFormat('F Y') }}</strong>
        </div>

        <div class="card">
            @foreach($budgets as $budget)
                @php
                    $pct = $budget->percentage;
                    $status = $budget->status;
                @endphp
                <div class="budget-item">
                    <div class="budget-info">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="table-row-icon" style="width:28px;height:28px;background:{{ $budget->category->color ?? '#6B7280' }}12;color:{{ $budget->category->color ?? '#6B7280' }};">
                                <i class="icon-{{ $budget->category->icon ?? 'circle' }}" style="font-size:13px;"></i>
                            </div>
                            <span class="budget-name">{{ $budget->category->name ?? '-' }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="budget-numbers">
                                Rp {{ number_format($budget->spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount, 0, ',', '.') }}
                            </span>
                            @if($status === 'exceeded')
                                <span class="badge badge--expense">Melebihi!</span>
                            @elseif($status === 'danger')
                                <span class="badge badge--expense">90%+</span>
                            @elseif($status === 'warning')
                                <span class="badge" style="background: rgba(245,158,11,0.1); color: #F59E0B;">75%+</span>
                            @endif
                            <form method="POST" action="{{ route('budgets.destroy', $budget) }}" data-confirm="Hapus anggaran ini?" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:4px;border:none;background:none;cursor:pointer;color:var(--n-text-muted);border-radius:6px;display:flex;" title="Hapus">
                                    <i class="icon-trash-2" style="font-size:13px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $status === 'danger' || $status === 'exceeded' ? 'progress-fill--danger' : ($status === 'warning' ? 'progress-fill--warning' : '') }}" style="width: {{ min($pct, 100) }}%;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                        <span style="font-size: 11px; color: var(--n-text-muted);">{{ $pct }}% terpakai</span>
                        <span style="font-size: 11px; color: var(--n-text-muted);">Sisa: Rp {{ number_format($budget->remaining, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <i class="icon-piggy-bank"></i>
                <div class="empty-state-title">Belum ada anggaran</div>
                <div class="empty-state-desc">Atur batas pengeluaran per kategori untuk bulan ini.</div>
                @if($availableCategories->count() > 0)
                    <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                        <i class="icon-plus"></i> Buat Anggaran Pertama
                    </button>
                @else
                    <p style="font-size: 12px; color: var(--n-text-muted); margin-top: 8px;">Tambahkan kategori pengeluaran terlebih dahulu.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Add Budget Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Anggaran</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">
                    <i class="icon-x" style="font-size:16px;"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('budgets.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Kategori Pengeluaran</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($availableCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batas Anggaran (Rp)</label>
                        <input type="text" name="amount" class="form-input" placeholder="500.000" data-currency required>
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
