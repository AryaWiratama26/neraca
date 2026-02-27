<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }

    public function collection()
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category', 'account'])
            ->orderBy('date', 'desc');

        if ($this->month && $this->year) {
            $query->whereMonth('date', $this->month)
                  ->whereYear('date', $this->year);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Tipe', 'Kategori', 'Akun', 'Jumlah', 'Catatan', 'Tag'];
    }

    public function map($tx): array
    {
        return [
            $tx->date->format('d/m/Y'),
            $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            $tx->category->name ?? 'Transfer',
            $tx->account->name ?? '-',
            $tx->amount,
            $tx->description ?? '',
            is_array($tx->tags) ? implode(', ', $tx->tags) : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
