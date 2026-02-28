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
            @php
                $icon = match($log->action) {
                    'created' => 'plus-circle',
                    'deleted' => 'trash-2',
                    'login' => 'log-in',
                    'logout' => 'log-out',
                    '2fa_enabled' => 'shield-check',
                    '2fa_disabled' => 'shield-alert',
                    'export' => 'download',
                    'import' => 'upload',
                    default => 'edit'
                };
                
                $colorHex = match($log->action) {
                    'created' => '#059669', // emerald
                    'deleted' => '#DC2626', // red
                    '2fa_enabled' => '#059669',
                    '2fa_disabled' => '#DC2626',
                    'login' => '#2563EB', // blue
                    'logout' => '#64748B', // slate
                    default => '#D97706' // amber (warning/updated)
                };

                $bg = match($log->action) {
                    'created', '2fa_enabled' => 'rgba(5,150,105,0.08)',
                    'deleted', '2fa_disabled' => 'rgba(220,38,38,0.08)',
                    'login' => 'rgba(37,99,235,0.08)',
                    'logout' => 'rgba(100,116,139,0.08)',
                    default => 'rgba(245,158,11,0.08)'
                };
            @endphp
            <div class="table-row">
                <div class="table-row-icon" style="background: {{ $bg }}; color: {{ $colorHex }};">
                    <i class="icon-{{ $icon }}" style="font-size:14px;"></i>
                </div>
                <div class="table-row-content">
                    <div class="table-row-title">{{ $log->description }}</div>
                    <div class="table-row-subtitle">
                        <span class="tag-badge" style="background: {{ $bg }}; color: {{ $colorHex }};">{{ $log->action_label }}</span>
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
