<x-layouts.app :title="'Laporan Keuangan'">

    {{-- Month selector + Export buttons --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('reports.index') }}" style="display: flex; align-items: center; gap: 8px;">
            <select name="month" class="form-select" style="width: auto;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endfor
            </select>
            <select name="year" class="form-select" style="width: auto;">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="icon-search"></i> Filter</button>
        </form>
        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <a href="{{ route('reports.export.pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary btn-sm">
                <i class="icon-file-text"></i> PDF
            </a>
            <a href="{{ route('reports.export.excel', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary btn-sm">
                <i class="icon-table"></i> Excel
            </a>
            <a href="{{ route('reports.export.csv', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary btn-sm">
                <i class="icon-file"></i> CSV
            </a>
            <a href="{{ route('reports.import') }}" class="btn btn-ghost btn-sm">
                <i class="icon-upload"></i> Import
            </a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--income"><i class="icon-trending-up"></i></div>
            <div class="stat-label">Total Pemasukan</div>
            <div class="stat-value" style="color: var(--n-income);">Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--expense"><i class="icon-trending-down"></i></div>
            <div class="stat-label">Total Pengeluaran</div>
            <div class="stat-value" style="color: var(--n-expense);">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--balance"><i class="icon-wallet"></i></div>
            <div class="stat-label">Selisih</div>
            <div class="stat-value" style="color: {{ $income - $expense >= 0 ? 'var(--n-income)' : 'var(--n-expense)' }};">
                {{ $income - $expense >= 0 ? '+' : '-' }}Rp {{ number_format(abs($income - $expense), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        {{-- Monthly Trend Chart --}}
        <div class="card">
            <div class="chart-header">
                <h2 class="chart-title">Tren 6 Bulan Terakhir</h2>
            </div>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Expense Breakdown Doughnut --}}
        <div class="card">
            <div class="chart-header">
                <h2 class="chart-title">Pengeluaran per Kategori</h2>
            </div>
            @if(count($chartLabels) > 0)
                <div style="padding: 20px; display: flex; justify-content: center;">
                    <div style="width: 220px; height: 220px;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            @else
                <div class="table-empty">Belum ada pengeluaran bulan ini.</div>
            @endif
        </div>
    </div>

    {{-- Category Breakdown Tables --}}
    <div style="margin-top: 14px;">
        <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="card">
                <div class="table-header">
                    <h2 class="table-title">Detail Pengeluaran</h2>
                </div>
                @forelse($expenseByCategory as $item)
                    <div class="table-row">
                        <div class="table-row-icon" style="background: {{ $item->category->color ?? '#DC2626' }}12; color: {{ $item->category->color ?? '#DC2626' }};">
                            <i class="icon-{{ $item->category->icon ?? 'circle' }}"></i>
                        </div>
                        <div class="table-row-content">
                            <div class="table-row-title">{{ $item->category->name ?? 'Lainnya' }}</div>
                        </div>
                        <div class="table-row-amount table-row-amount--expense">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="table-empty">Tidak ada data.</div>
                @endforelse
            </div>

            <div class="card">
                <div class="table-header">
                    <h2 class="table-title">Detail Pemasukan</h2>
                </div>
                @forelse($incomeByCategory as $item)
                    <div class="table-row">
                        <div class="table-row-icon" style="background: {{ $item->category->color ?? '#059669' }}12; color: {{ $item->category->color ?? '#059669' }};">
                            <i class="icon-{{ $item->category->icon ?? 'circle' }}"></i>
                        </div>
                        <div class="table-row-content">
                            <div class="table-row-title">{{ $item->category->name ?? 'Lainnya' }}</div>
                        </div>
                        <div class="table-row-amount table-row-amount--income">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="table-empty">Tidak ada data.</div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trend Chart
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($trendLabels),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: @json($trendIncome),
                                backgroundColor: 'rgba(5, 150, 105, 0.7)',
                                borderRadius: 6,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: @json($trendExpense),
                                backgroundColor: 'rgba(220, 38, 38, 0.6)',
                                borderRadius: 6,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top', align: 'end',
                                labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', font: { family: "'Inter'", size: 11 }, padding: 14, color: '#6B7280' }
                            },
                            tooltip: {
                                backgroundColor: '#1a1a1a', cornerRadius: 10, padding: 10,
                                titleFont: { family: "'Inter'", size: 12 },
                                bodyFont: { family: "'Inter'", size: 12 },
                                displayColors: false,
                                callbacks: { label: function(c) { return c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID'); } }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: "'Inter'", size: 10 }, color: '#9CA3AF' }, border: { display: false } },
                            y: {
                                grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                                ticks: {
                                    font: { family: "'Inter'", size: 10 }, color: '#9CA3AF',
                                    callback: function(v) { if(v>=1e6) return (v/1e6)+'jt'; if(v>=1e3) return (v/1e3)+'rb'; return v; }
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // Expense Doughnut
            const expCtx = document.getElementById('expenseChart');
            if (expCtx) {
                new Chart(expCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            data: @json($chartData),
                            backgroundColor: @json($chartColors),
                            borderWidth: 0,
                            spacing: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', font: { family: "'Inter'", size: 11 }, padding: 10, color: '#6B7280' }
                            },
                            tooltip: {
                                backgroundColor: '#1a1a1a', cornerRadius: 10, padding: 10,
                                bodyFont: { family: "'Inter'", size: 12 },
                                callbacks: { label: function(c) { return c.label + ': Rp ' + c.parsed.toLocaleString('id-ID'); } }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
