<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan — {{ $date->translatedFormat('F Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a2e; line-height: 1.5; padding: 30px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #0F766E; padding-bottom: 14px; }
        .header h1 { font-size: 20px; color: #0F766E; margin-bottom: 2px; }
        .header p { font-size: 12px; color: #6B7280; }
        .summary { display: flex; margin-bottom: 20px; }
        .summary-card { flex: 1; text-align: center; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin: 0 4px; }
        .summary-card .label { font-size: 10px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { font-size: 16px; font-weight: 700; margin-top: 4px; }
        .income { color: #059669; }
        .expense { color: #DC2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #0F766E; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
        tr:nth-child(even) { background: #f8fffe; }
        .text-right { text-align: right; }
        .section-title { font-size: 13px; font-weight: 600; margin: 20px 0 8px; color: #0F766E; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #9CA3AF; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Neraca — Laporan Keuangan</h1>
        <p>Periode: {{ $date->translatedFormat('F Y') }}</p>
    </div>

    <table style="margin-bottom: 20px;">
        <tr>
            <td style="text-align:center; border:1px solid #e5e7eb; border-radius: 6px; padding: 12px; width: 33%;">
                <div style="font-size:10px; color:#6B7280;">PEMASUKAN</div>
                <div style="font-size:16px; font-weight:700; color:#059669; margin-top:4px;">Rp {{ number_format($income, 0, ',', '.') }}</div>
            </td>
            <td style="text-align:center; border:1px solid #e5e7eb; border-radius: 6px; padding: 12px; width: 33%;">
                <div style="font-size:10px; color:#6B7280;">PENGELUARAN</div>
                <div style="font-size:16px; font-weight:700; color:#DC2626; margin-top:4px;">Rp {{ number_format($expense, 0, ',', '.') }}</div>
            </td>
            <td style="text-align:center; border:1px solid #e5e7eb; border-radius: 6px; padding: 12px; width: 33%;">
                <div style="font-size:10px; color:#6B7280;">SELISIH</div>
                <div style="font-size:16px; font-weight:700; color:{{ $income - $expense >= 0 ? '#059669' : '#DC2626' }}; margin-top:4px;">
                    Rp {{ number_format(abs($income - $expense), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    @if($expenseByCategory->count())
    <div class="section-title">Pengeluaran per Kategori</div>
    <table>
        <thead>
            <tr><th>Kategori</th><th class="text-right">Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($expenseByCategory as $item)
            <tr>
                <td>{{ $item->category->name ?? 'Lainnya' }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($incomeByCategory->count())
    <div class="section-title">Pemasukan per Kategori</div>
    <table>
        <thead>
            <tr><th>Kategori</th><th class="text-right">Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($incomeByCategory as $item)
            <tr>
                <td>{{ $item->category->name ?? 'Lainnya' }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="section-title">Detail Transaksi</div>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>Tipe</th><th>Kategori</th><th>Akun</th><th class="text-right">Jumlah</th><th>Catatan</th></tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->date->format('d/m/Y') }}</td>
                <td>{{ $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                <td>{{ $tx->category->name ?? 'Transfer' }}</td>
                <td>{{ $tx->account->name ?? '-' }}</td>
                <td class="text-right {{ $tx->type === 'income' ? 'income' : 'expense' }}">
                    {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
                <td>{{ Str::limit($tx->description, 30) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} — Neraca Financial App
    </div>
</body>
</html>
