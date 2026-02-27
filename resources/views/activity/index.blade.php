<x-layouts.app :title="'Riwayat Aktivitas'">

    <div style="display: flex; gap: 8px; margin-bottom: 14px;">
        <form method="GET" action="{{ route('activity.index') }}" style="display: flex; gap: 8px;">
            <select name="action" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Aktivitas</option>
                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Dibuat</option>
                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Diperbarui</option>
                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="export" {{ request('action') === 'export' ? 'selected' : '' }}>Export</option>
                <option value="import" {{ request('action') === 'import' ? 'selected' : '' }}>Import</option>
            </select>
        </form>
    </div>

    <div class="card">
        @forelse($logs as $log)
            <div class="table-row">
                <div class="table-row-icon" style="background:
                    {{ $log->action === 'created' ? 'rgba(5,150,105,0.08)' : ($log->action === 'deleted' ? 'rgba(220,38,38,0.08)' : 'rgba(245,158,11,0.08)') }};
                    color: {{ $log->action === 'created' ? '#059669' : ($log->action === 'deleted' ? '#DC2626' : '#D97706') }};">
                    <i class="icon-{{ $log->action === 'created' ? 'plus-circle' : ($log->action === 'deleted' ? 'trash-2' : ($log->action === 'login' ? 'log-in' : 'edit')) }}" style="font-size:14px;"></i>
                </div>
                <div class="table-row-content">
                    <div class="table-row-title">{{ $log->description }}</div>
                    <div class="table-row-subtitle">
                        <span class="tag-badge" style="background: {{ $log->action === 'created' ? 'rgba(5,150,105,0.1)' : ($log->action === 'deleted' ? 'rgba(220,38,38,0.1)' : 'rgba(245,158,11,0.1)') }}; color: {{ $log->action === 'created' ? '#059669' : ($log->action === 'deleted' ? '#DC2626' : '#D97706') }};">{{ $log->action_label }}</span>
                        {{ $log->created_at->format('d M Y H:i') }}
                        @if($log->ip_address) &middot; {{ $log->ip_address }} @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="icon-activity"></i>
                <div class="empty-state-title">Belum ada aktivitas</div>
                <div class="empty-state-desc">Riwayat perubahan akan tercatat di sini.</div>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div style="margin-top: 16px; display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</x-layouts.app>
