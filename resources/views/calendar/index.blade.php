<x-layouts.app :title="'Kalender Keuangan'">

    {{-- Month Navigation --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
        @php
            $prev = $date->copy()->subMonth();
            $next = $date->copy()->addMonth();
        @endphp
        <a href="{{ route('calendar.index', ['month' => $prev->month, 'year' => $prev->year]) }}" class="btn btn-ghost btn-sm">
            <i class="icon-chevron-left"></i>
        </a>
        <h2 style="font-size: 16px; font-weight: 600;">{{ $date->translatedFormat('F Y') }}</h2>
        <a href="{{ route('calendar.index', ['month' => $next->month, 'year' => $next->year]) }}" class="btn btn-ghost btn-sm">
            <i class="icon-chevron-right"></i>
        </a>
    </div>

    {{-- Month Summary --}}
    <div class="stats-grid" style="margin-bottom: 16px;">
        <div class="stat-card">
            <div class="stat-icon stat-icon--income"><i class="icon-trending-up"></i></div>
            <div class="stat-label">Pemasukan</div>
            <div class="stat-value" style="color: var(--n-income); font-size: 16px;">Rp {{ number_format($monthIncome, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--expense"><i class="icon-trending-down"></i></div>
            <div class="stat-label">Pengeluaran</div>
            <div class="stat-value" style="color: var(--n-expense); font-size: 16px;">Rp {{ number_format($monthExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Calendar Grid --}}
    <div class="card" style="overflow: hidden;">
        <div class="calendar-grid">
            {{-- Day headers --}}
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                <div class="calendar-header">{{ $day }}</div>
            @endforeach

            {{-- Empty cells before first day --}}
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="calendar-cell calendar-cell--empty"></div>
            @endfor

            {{-- Day cells --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $isToday = ($d === now()->day && $month == now()->month && $year == now()->year);
                    $hasTx = isset($transactionsByDay[$d]);
                    $totals = $dailyTotals[$d] ?? ['income' => 0, 'expense' => 0];
                @endphp
                <div class="calendar-cell {{ $isToday ? 'calendar-cell--today' : '' }} {{ $hasTx ? 'calendar-cell--has-data' : '' }}"
                     @if($hasTx) onclick="showDayDetail({{ $d }})" style="cursor:pointer;" @endif>
                    <div class="calendar-day {{ $isToday ? 'calendar-day--today' : '' }}">{{ $d }}</div>
                    @if($hasTx)
                        <div class="calendar-amounts">
                            @if($totals['income'] > 0)
                                <div class="calendar-amount calendar-amount--income">+@php echo $totals['income'] >= 1000000 ? round($totals['income']/1000000, 1).'jt' : ($totals['income'] >= 1000 ? round($totals['income']/1000).'rb' : number_format($totals['income'], 0, ',', '.')); @endphp</div>
                            @endif
                            @if($totals['expense'] > 0)
                                <div class="calendar-amount calendar-amount--expense">-@php echo $totals['expense'] >= 1000000 ? round($totals['expense']/1000000, 1).'jt' : ($totals['expense'] >= 1000 ? round($totals['expense']/1000).'rb' : number_format($totals['expense'], 0, ',', '.')); @endphp</div>
                            @endif
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    {{-- Day Detail Modal --}}
    <div class="modal-overlay" id="dayModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Transaksi <span id="dayTitle"></span></h2>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')"><i class="icon-x" style="font-size:16px;"></i></button>
            </div>
            <div class="modal-body" id="dayContent" style="max-height: 400px; overflow-y: auto;"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        const txByDay = @json($transactionsByDay);

        function showDayDetail(day) {
            const txs = txByDay[day] || [];
            document.getElementById('dayTitle').textContent = day + ' {{ $date->translatedFormat("F Y") }}';

            let html = '';
            if (txs.length === 0) {
                html = '<div style="text-align:center;color:var(--n-text-muted);padding:20px;">Tidak ada transaksi.</div>';
            } else {
                txs.forEach(tx => {
                    const isIncome = tx.type === 'income';
                    const amount = parseFloat(tx.amount || 0);
                    html += `<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--n-border);">
                        <div>
                            <div style="font-size:13px;font-weight:500;">${tx.category?.name || 'Transfer'}</div>
                            <div style="font-size:11px;color:var(--n-text-muted);">${tx.account?.name || '-'}${tx.description ? ' · ' + tx.description : ''}</div>
                            ${tx.tags && tx.tags.length ? '<div style="margin-top:3px;">' + tx.tags.map(t => '<span class="tag-badge">' + t + '</span>').join('') + '</div>' : ''}
                        </div>
                        <div style="font-size:13px;font-weight:600;color:${isIncome ? 'var(--n-income)' : 'var(--n-expense)'};">
                            ${isIncome ? '+' : '-'}Rp ${amount.toLocaleString('id-ID')}
                        </div>
                    </div>`;
                });
            }

            document.getElementById('dayContent').innerHTML = html;
            document.getElementById('dayModal').classList.add('open');
        }
    </script>
    @endpush
</x-layouts.app>
