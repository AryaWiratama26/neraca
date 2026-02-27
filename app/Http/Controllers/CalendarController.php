<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $month = $request->input('month', $now->month);
        $year = $request->input('year', $now->year);

        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $firstDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek; // 0=Sunday

        // Fetch all transactions for this month
        $transactions = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with(['category', 'account'])
            ->orderBy('date')
            ->get();

        // Group by day
        $transactionsByDay = [];
        foreach ($transactions as $tx) {
            $day = $tx->date->day;
            $transactionsByDay[$day][] = $tx;
        }

        // Daily totals
        $dailyTotals = [];
        foreach ($transactionsByDay as $day => $txs) {
            $dailyTotals[$day] = [
                'income' => collect($txs)->where('type', 'income')->sum('amount'),
                'expense' => collect($txs)->where('type', 'expense')->sum('amount'),
            ];
        }

        // Month summary
        $monthIncome = $transactions->where('type', 'income')->sum('amount');
        $monthExpense = $transactions->where('type', 'expense')->sum('amount');

        return view('calendar.index', compact(
            'date', 'month', 'year', 'daysInMonth', 'firstDayOfWeek',
            'transactionsByDay', 'dailyTotals', 'monthIncome', 'monthExpense'
        ));
    }
}
