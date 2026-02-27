<x-layouts.app :title="'Target Tabungan'">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')">
            <i class="icon-plus"></i> Buat Target
        </button>
    </x-slot:actions>

    @if($goals->count() > 0)
        <div class="stats-grid" style="margin-bottom: 20px;">
            <div class="stat-card">
                <div class="stat-icon stat-icon--balance"><i class="icon-target"></i></div>
                <div class="stat-label">Total Target</div>
                <div class="stat-value">Rp {{ number_format($totalTarget, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--income"><i class="icon-check-circle"></i></div>
                <div class="stat-label">Total Terkumpul</div>
                <div class="stat-value" style="color: var(--n-income);">Rp {{ number_format($totalSaved, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--expense"><i class="icon-clock"></i></div>
                <div class="stat-label">Sisa Kebutuhan</div>
                <div class="stat-value" style="color: var(--n-expense);">Rp {{ number_format($totalTarget - $totalSaved, 0, ',', '.') }}</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px;">
            @foreach($goals as $goal)
                @php $progress = $goal->progress; @endphp
                <div class="card card-padding">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="stat-icon" style="background: {{ $goal->color ?? '#0F766E' }}12; color: {{ $goal->color ?? '#0F766E' }}; margin-bottom: 0; width: 38px; height: 38px;">
                                <i class="icon-{{ $goal->icon ?? 'target' }}"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 600;">{{ $goal->name }}</div>
                                @if($goal->deadline)
                                    <div style="font-size: 11.5px; color: var(--n-text-muted);">
                                        Deadline: {{ $goal->deadline->format('d M Y') }}
                                        @if($goal->deadline->isPast())
                                            <span style="color: var(--n-expense);">(lewat)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="table-row-actions">
                            <form method="POST" action="{{ route('goals.destroy', $goal) }}" data-confirm="Hapus target ini?" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="delete-btn"><i class="icon-trash-2" style="font-size:14px;"></i></button>
                            </form>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 12px; color: var(--n-text-secondary);">
                            Rp {{ number_format($goal->current_amount, 0, ',', '.') }}
                        </span>
                        <span style="font-size: 12px; color: var(--n-text-muted);">
                            Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="progress-bar" style="height: 8px; margin-bottom: 12px;">
                        <div class="progress-fill" style="width: {{ $progress }}%; {{ $progress >= 100 ? 'background: linear-gradient(90deg, #059669, #10B981);' : '' }}"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12px; font-weight: 600; color: {{ $progress >= 100 ? 'var(--n-income)' : 'var(--n-text-secondary)' }};">
                            {{ $progress }}%
                            @if($progress >= 100) ✓ Tercapai! @endif
                        </span>
                        @if($progress < 100)
                            <button class="btn btn-primary btn-sm" onclick="openFundModal({{ $goal->id }}, '{{ $goal->name }}')">
                                <i class="icon-plus"></i> Tambah Dana
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <i class="icon-target"></i>
                <div class="empty-state-title">Belum ada target tabungan</div>
                <div class="empty-state-desc">Buat target untuk memotivasi Anda menabung.</div>
                <button class="btn-empty-state" onclick="document.getElementById('addModal').classList.add('open')">
                    <i class="icon-plus"></i> Buat Target Pertama
                </button>
            </div>
        </div>
    @endif

    {{-- Add Goal Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Buat Target Tabungan</h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" action="{{ route('goals.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Target</label>
                        <input type="text" name="name" class="form-input" placeholder="contoh: Liburan, Dana Darurat" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah Target (Rp)</label>
                            <input type="text" name="target_amount" class="form-input" placeholder="5.000.000" data-currency required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deadline (opsional)</label>
                            <input type="date" name="deadline" class="form-input">
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

    {{-- Fund Goal Modal --}}
    <div class="modal-overlay" id="fundModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Dana ke <span id="fundGoalName"></span></h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <form method="POST" id="fundForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Jumlah Dana (Rp)</label>
                        <input type="text" name="amount" class="form-input" placeholder="100.000" data-currency required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.modal-overlay').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openFundModal(goalId, goalName) {
            document.getElementById('fundGoalName').textContent = goalName;
            document.getElementById('fundForm').action = '/goals/' + goalId + '/fund';
            document.getElementById('fundModal').classList.add('open');
        }
    </script>
    @endpush
</x-layouts.app>
