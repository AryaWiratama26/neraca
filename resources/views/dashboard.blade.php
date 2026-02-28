<x-layouts.app :title="'Dashboard'">

    {{-- Budget Alerts --}}
    @if($budgetAlerts->count() > 0)
        <div style="margin-bottom: 16px;">
            @foreach($budgetAlerts as $alert)
                <div class="budget-alert {{ $alert->status === 'exceeded' ? 'budget-alert--danger' : ($alert->status === 'danger' ? 'budget-alert--danger' : 'budget-alert--warning') }}">
                    <i class="icon-alert-triangle" style="font-size: 14px;"></i>
                    <span>
                        @if($alert->status === 'exceeded')
                            <strong>Anggaran {{ $alert->category->name ?? '' }} melebihi batas!</strong> ({{ $alert->percentage }}% terpakai)
                        @elseif($alert->status === 'danger')
                            <strong>Anggaran {{ $alert->category->name ?? '' }} hampir habis!</strong> ({{ $alert->percentage }}% terpakai)
                        @else
                            Anggaran {{ $alert->category->name ?? '' }} sudah {{ $alert->percentage }}% terpakai.
                        @endif
                        — Sisa: Rp {{ number_format($alert->remaining, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--balance"><i class="icon-wallet"></i></div>
            <div class="stat-label">Total Saldo</div>
            <div class="stat-value">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--income"><i class="icon-trending-up"></i></div>
            <div class="stat-label">Pemasukan Bulan Ini</div>
            <div class="stat-value" style="color: var(--n-income);">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--expense"><i class="icon-trending-down"></i></div>
            <div class="stat-label">Pengeluaran Bulan Ini</div>
            <div class="stat-value" style="color: var(--n-expense);">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700">Dashboard Finansial</h1>
        <p class="text-slate-500 mt-1">Ringkasan kondisi keuangan Anda saat ini.</p>
    </div>

    @if(isset($announcements) && $announcements->count() > 0)
        <div class="mb-8 space-y-3">
            @php
                $typeConfig = [
                    'info' => ['icon' => 'info', 'accent' => '#3B82F6'],
                    'warning' => ['icon' => 'alert-triangle', 'accent' => '#F59E0B'],
                    'success' => ['icon' => 'check-circle', 'accent' => '#10B981'],
                    'danger' => ['icon' => 'alert-circle', 'accent' => '#EF4444'],
                    'default' => ['icon' => 'bell', 'accent' => 'var(--n-primary)'],
                ];
            @endphp

            @foreach($announcements as $announcement)
                @php $cfg = $typeConfig[$announcement->type] ?? $typeConfig['default']; @endphp
                <div class="card" style="padding: 14px 20px; display: flex; align-items: center; gap: 18px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: var(--n-primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-{{ $cfg['icon'] }}" style="font-size: 20px; color: {{ $cfg['accent'] }};"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                            <h4 style="font-size: 14px; font-weight: 700; color: var(--n-text); margin: 0;">{{ $announcement->title }}</h4>
                            <span style="font-size: 10px; font-weight: 700; color: var(--n-text-muted); text-transform: uppercase; letter-spacing: 0.05em; background: rgba(0,0,0,0.04); padding: 1px 6px; border-radius: 4px;">Sistem</span>
                        </div>
                        <p style="font-size: 13.5px; color: var(--n-text-secondary); margin: 0; line-height: 1.4;">{{ $announcement->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="dashboard-grid" id="widgetRow1">
        <div class="card widget-card" data-widget="cashflow" draggable="true">
            <div class="chart-header">
                <h2 class="chart-title">Arus Kas 7 Hari Terakhir</h2>
                <span class="widget-drag-handle" title="Seret untuk mengatur ulang">&#10303;</span>
            </div>
            <div class="chart-container">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <div class="card widget-card" data-widget="top5" draggable="true">
            <div class="chart-header">
                <h2 class="chart-title">Top 5 Pengeluaran</h2>
                <span class="widget-drag-handle" title="Seret untuk mengatur ulang">&#10303;</span>
            </div>
            @if(count($top5Labels) > 0)
                <div style="padding: 20px; display: flex; justify-content: center;">
                    <div style="width: 220px; height: 220px;">
                        <canvas id="top5Chart"></canvas>
                    </div>
                </div>
            @else
                <div class="table-empty">
                    <i class="icon-pie-chart"></i>
                    Belum ada data pengeluaran bulan ini.
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Transactions + Budget Status --}}
    <div class="dashboard-grid" style="margin-top: 14px;">
        <div class="card table-container">
            <div class="table-header">
                <h2 class="table-title">Transaksi Terakhir</h2>
                @if($recentTransactions->count() > 0)
                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
                @endif
            </div>
            @forelse($recentTransactions as $tx)
                <div class="table-row">
                    <div class="table-row-icon" style="background: {{ $tx->category->color ?? '#6B7280' }}12; color: {{ $tx->category->color ?? '#6B7280' }};">
                        <i class="icon-{{ $tx->type === 'income' ? 'arrow-up-right' : 'arrow-down-left' }}"></i>
                    </div>
                    <div class="table-row-content">
                        <div class="table-row-title">{{ $tx->category->name ?? 'Transfer' }}</div>
                        <div class="table-row-subtitle">{{ $tx->account->name ?? '-' }} &middot; {{ $tx->date->format('d M Y') }}</div>
                    </div>
                    <div class="table-row-amount {{ $tx->type === 'income' ? 'table-row-amount--income' : 'table-row-amount--expense' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="table-empty">
                    <i class="icon-file-text"></i>
                    Belum ada transaksi.
                </div>
            @endforelse
        </div>

        @if($budgets->count() > 0)
            <div class="card">
                <div class="table-header">
                    <h2 class="table-title">Status Anggaran Bulan Ini</h2>
                </div>
                @foreach($budgets as $budget)
                    <div class="budget-item">
                        <div class="budget-info">
                            <span class="budget-name">{{ $budget->category->name ?? '-' }}</span>
                            <span class="budget-numbers">Rp {{ number_format($budget->spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill {{ $budget->percentage >= 90 ? 'progress-fill--danger' : ($budget->percentage >= 75 ? 'progress-fill--warning' : '') }}" style="width: {{ min($budget->percentage, 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cash Flow Chart
            const ctx = document.getElementById('cashFlowChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Pemasukan', data: @json($chartIncome),
                                borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.06)',
                                borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#059669',
                            },
                            {
                                label: 'Pengeluaran', data: @json($chartExpense),
                                borderColor: '#DC2626', backgroundColor: 'rgba(220, 38, 38, 0.04)',
                                borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#DC2626',
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { position: 'top', align: 'end', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', font: { family: "'Inter'", size: 11 }, padding: 14, color: '#6B7280' } },
                            tooltip: { backgroundColor: '#1a1a1a', cornerRadius: 10, padding: 10, displayColors: false, titleFont: { family: "'Inter'", size: 12 }, bodyFont: { family: "'Inter'", size: 12 },
                                callbacks: { label: function(c) { return c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID'); } }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: "'Inter'", size: 10 }, color: '#9CA3AF' }, border: { display: false } },
                            y: { grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false }, ticks: { font: { family: "'Inter'", size: 10 }, color: '#9CA3AF', callback: function(v) { if(v>=1e6)return(v/1e6)+'jt';if(v>=1e3)return(v/1e3)+'rb';return v; } }, border: { display: false } }
                        }
                    }
                });
            }

            // Top 5 Doughnut
            const top5 = document.getElementById('top5Chart');
            if (top5 && @json(count($top5Labels)) > 0) {
                new Chart(top5, {
                    type: 'doughnut',
                    data: {
                        labels: @json($top5Labels),
                        datasets: [{
                            data: @json($top5Data),
                            backgroundColor: @json($top5Colors),
                            borderWidth: 0, spacing: 2
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: true, cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', font: { family: "'Inter'", size: 11 }, padding: 10, color: '#6B7280' } },
                            tooltip: { backgroundColor: '#1a1a1a', cornerRadius: 10, padding: 10, bodyFont: { family: "'Inter'", size: 12 },
                                callbacks: { label: function(c) { return c.label + ': Rp ' + c.parsed.toLocaleString('id-ID'); } }
                            }
                        }
                    }
                });
            }

            // Widget drag & drop
            document.querySelectorAll('.widget-card').forEach(card => {
                card.addEventListener('dragstart', e => {
                    e.dataTransfer.setData('text/plain', card.dataset.widget);
                    card.style.opacity = '0.5';
                });
                card.addEventListener('dragend', () => { card.style.opacity = '1'; });
                card.addEventListener('dragover', e => { e.preventDefault(); card.style.outline = '2px dashed var(--n-primary)'; });
                card.addEventListener('dragleave', () => { card.style.outline = 'none'; });
                card.addEventListener('drop', e => {
                    e.preventDefault();
                    card.style.outline = 'none';
                    const fromId = e.dataTransfer.getData('text/plain');
                    const fromEl = document.querySelector(`[data-widget="${fromId}"]`);
                    const parent = card.parentNode;
                    if (fromEl && fromEl !== card) {
                        const fromNext = fromEl.nextElementSibling;
                        parent.insertBefore(fromEl, card);
                        if (fromNext) parent.insertBefore(card, fromNext);
                        else parent.appendChild(card);
                        // Save layout
                        const layout = [...parent.querySelectorAll('.widget-card')].map(w => w.dataset.widget);
                        fetch('/dashboard/layout', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                            body: JSON.stringify({ layout })
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-layouts.app>
