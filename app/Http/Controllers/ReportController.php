<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Exports\TransactionsExport;
use App\Imports\TransactionsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $month = $request->input('month', $now->month);
        $year = $request->input('year', $now->year);

        $date = Carbon::createFromDate($year, $month, 1);

        // Fetch all month transactions once (encrypted—PHP-level aggregation)
        $monthTx = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with('category')
            ->get();

        $income = $monthTx->where('type', 'income')->sum('amount');
        $expense = $monthTx->where('type', 'expense')->sum('amount');

        $expenseByCategory = $this->groupByCategory($monthTx, 'expense');
        $incomeByCategory = $this->groupByCategory($monthTx, 'income');

        // Monthly trend (last 6 months)
        $trendLabels = [];
        $trendIncome = [];
        $trendExpense = [];

        $sixMonthsTx = Transaction::where('user_id', Auth::id())
            ->whereDate('date', '>=', $now->copy()->subMonths(5)->startOfMonth()->toDateString())
            ->get();

        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $trendLabels[] = $m->translatedFormat('M Y');

            $monthly = $sixMonthsTx->filter(fn($tx) => $tx->date->month === $m->month && $tx->date->year === $m->year);
            $trendIncome[] = (float) $monthly->where('type', 'income')->sum('amount');
            $trendExpense[] = (float) $monthly->where('type', 'expense')->sum('amount');
        }

        $chartLabels = $expenseByCategory->map(fn($e) => $e->category->name ?? 'Lainnya')->toArray();
        $chartData = $expenseByCategory->pluck('total')->map(fn($v) => (float)$v)->toArray();
        $chartColors = $expenseByCategory->map(fn($e) => $e->category->color ?? '#6B7280')->toArray();

        return view('reports.index', compact(
            'date', 'month', 'year', 'income', 'expense',
            'expenseByCategory', 'incomeByCategory',
            'trendLabels', 'trendIncome', 'trendExpense',
            'chartLabels', 'chartData', 'chartColors'
        ));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $date = Carbon::createFromDate($year, $month, 1);

        $transactions = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $expenseByCategory = $this->groupByCategory($transactions, 'expense');
        $incomeByCategory = $this->groupByCategory($transactions, 'income');

        $pdf = Pdf::loadView('reports.pdf', compact(
            'date', 'transactions', 'income', 'expense', 'expenseByCategory', 'incomeByCategory'
        ));

        return $pdf->download("neraca-laporan-{$date->format('Y-m')}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return Excel::download(
            new TransactionsExport($month, $year),
            "neraca-transaksi-{$year}-{$month}.xlsx"
        );
    }

    public function exportCsv(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return Excel::download(
            new TransactionsExport($month, $year),
            "neraca-transaksi-{$year}-{$month}.csv",
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function importForm()
    {
        return view('reports.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:5120',
        ]);

        $import = new TransactionsImport();
        Excel::import($import, $request->file('file'));

        $msg = "{$import->imported} transaksi berhasil diimpor.";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} baris dilewati.";
        }

        if (count($import->errors) > 0) {
            return redirect()->route('reports.import')
                ->with('success', $msg)
                ->with('import_errors', $import->errors);
        }

        return redirect()->route('reports.index')->with('success', $msg);
    }

    private function groupByCategory($transactions, $type)
    {
        return $transactions->where('type', $type)
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->map(function ($group) {
                return (object) [
                    'category' => $group->first()->category,
                    'total' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }
}
