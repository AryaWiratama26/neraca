<x-layouts.app :title="'Prediksi Pengeluaran'">

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--expense"><i class="icon-trending-down"></i></div>
            <div class="stat-label">Rata-rata Pengeluaran/Bulan</div>
            <div class="stat-value" style="color: var(--n-expense);">Rp {{ number_format($avgExpense, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--income"><i class="icon-trending-up"></i></div>
            <div class="stat-label">Rata-rata Pemasukan/Bulan</div>
            <div class="stat-value" style="color: var(--n-income);">Rp {{ number_format($avgIncome, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--balance"><i class="icon-target"></i></div>
            <div class="stat-label">Proyeksi Bulan Ini</div>
            <div class="stat-value" style="color: var(--n-expense);">Rp {{ number_format($projectedExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="dashboard-grid">
        {{-- Trend + Prediction Chart --}}
        <div class="card">
            <div class="chart-header">
                <h2 class="chart-title">Tren & Prediksi {{ $nextMonth->translatedFormat('F') }}</h2>
            </div>
            <div class="chart-container">
                <canvas id="predictionChart"></canvas>
            </div>
        </div>

        {{-- Category Prediction Breakdown --}}
        <div class="card">
            <div class="chart-header">
                <h2 class="chart-title">Estimasi per Kategori</h2>
            </div>
            @if(count($predLabels) > 0)
                <div style="padding: 20px; display: flex; justify-content: center;">
                    <div style="width: 220px; height: 220px;">
                        <canvas id="catPredChart"></canvas>
                    </div>
                </div>
            @else
                <div class="table-empty">Belum cukup data untuk prediksi.</div>
            @endif
        </div>
    </div>

    {{-- Current Month Progress --}}
    <div style="margin-top: 14px;">
        <div class="card">
            <div class="table-header">
                <h2 class="table-title">Pengeluaran Bulan Ini vs Rata-rata</h2>
            </div>
            <div class="budget-item">
                <div class="budget-info">
                    <span class="budget-name">Progress Bulan {{ $now->translatedFormat('F') }}</span>
                    <span class="budget-numbers">Rp {{ number_format($currentMonthExpense, 0, ',', '.') }} / Rp {{ number_format($avgExpense, 0, ',', '.') }}</span>
                </div>
                @php $pct = $avgExpense > 0 ? min(($currentMonthExpense / $avgExpense) * 100, 120) : 0; @endphp
                <div class="progress-bar">
                    <div class="progress-fill {{ $pct >= 100 ? 'progress-fill--danger' : ($pct >= 80 ? 'progress-fill--warning' : '') }}" style="width: {{ min($pct, 100) }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Breakdown Table --}}
    @if($categoryPredictions->count() > 0)
    <div class="card" style="margin-top: 14px;">
        <div class="table-header">
            <h2 class="table-title">Prediksi Detail per Kategori</h2>
        </div>
        @foreach($categoryPredictions as $pred)
            <div class="table-row">
                <div class="table-row-icon" style="background: {{ $pred->category->color ?? '#6B7280' }}12; color: {{ $pred->category->color ?? '#6B7280' }};">
                    <i class="icon-{{ $pred->category->icon ?? 'circle' }}"></i>
                </div>
                <div class="table-row-content">
                    <div class="table-row-title">{{ $pred->category->name ?? 'Lainnya' }}</div>
                    <div class="table-row-subtitle">{{ $pred->count }} transaksi / 3 bulan</div>
                </div>
                <div class="table-row-amount table-row-amount--expense">
                    ~Rp {{ number_format($pred->avg_monthly, 0, ',', '.') }}/bln
                </div>
            </div>
        @endforeach
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($trendLabels);
        const lastIdx = labels.length - 1;

        // Trend + Prediction Chart
        new Chart(document.getElementById('predictionChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan', data: @json($trendIncome),
                        backgroundColor: labels.map((_, i) => i === lastIdx ? 'rgba(5,150,105,0.3)' : 'rgba(5,150,105,0.7)'),
                        borderRadius: 6, barPercentage: 0.6,
                        borderWidth: labels.map((_, i) => i === lastIdx ? 2 : 0),
                        borderColor: 'rgba(5,150,105,0.6)', borderDash: [4,4],
                    },
                    {
                        label: 'Pengeluaran', data: @json($trendExpense),
                        backgroundColor: labels.map((_, i) => i === lastIdx ? 'rgba(220,38,38,0.25)' : 'rgba(220,38,38,0.6)'),
                        borderRadius: 6, barPercentage: 0.6,
                        borderWidth: labels.map((_, i) => i === lastIdx ? 2 : 0),
                        borderColor: 'rgba(220,38,38,0.5)', borderDash: [4,4],
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, pointStyle: 'circle', font: { family: "'Inter'", size: 11 }, color: '#6B7280' } },
                    tooltip: { backgroundColor: '#1a1a1a', cornerRadius: 10, padding: 10, displayColors: false,
                        callbacks: { label: c => c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: "'Inter'", size: 10 }, color: '#9CA3AF' }, border: { display: false } },
                    y: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { family: "'Inter'", size: 10 }, color: '#9CA3AF', callback: v => v>=1e6?(v/1e6)+'jt':v>=1e3?(v/1e3)+'rb':v }, border: { display: false } }
                }
            }
        });

        // Category Prediction Doughnut
        const catCtx = document.getElementById('catPredChart');
        if (catCtx && @json(count($predLabels)) > 0) {
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($predLabels),
                    datasets: [{ data: @json($predData), backgroundColor: @json($predColors), borderWidth: 0, spacing: 2 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: true, cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 8, usePointStyle: true, font: { family: "'Inter'", size: 11 }, color: '#6B7280' } },
                        tooltip: { backgroundColor: '#1a1a1a', cornerRadius: 10, callbacks: { label: c => c.label + ': ~Rp ' + c.parsed.toLocaleString('id-ID') + '/bln' } }
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-layouts.app>
