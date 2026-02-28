<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Fetch all month transactions once
        $monthTransactions = Transaction::where('user_id', $user->id)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->with('category')
            ->get();

        $incomeThisMonth = $monthTransactions->where('type', 'income')->sum('amount');
        $expenseThisMonth = $monthTransactions->where('type', 'expense')->sum('amount');

        $totalBalance = Account::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->sum('balance');

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->get();

        // Budget alerts (warning, danger, exceeded)
        $budgetAlerts = $budgets->filter(fn($b) => in_array($b->status, ['warning', 'danger', 'exceeded']));

        // Top 5 expense categories this month (for doughnut chart)
        $top5Expense = $monthTransactions->where('type', 'expense')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->map(function ($group) {
                return (object) [
                    'name' => $group->first()->category->name ?? 'Lainnya',
                    'color' => $group->first()->category->color ?? '#6B7280',
                    'total' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $top5Labels = $top5Expense->pluck('name')->toArray();
        $top5Data = $top5Expense->pluck('total')->map(fn($v) => (float)$v)->toArray();
        $top5Colors = $top5Expense->pluck('color')->toArray();

        // Chart data: last 7 days
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        $weekTransactions = Transaction::where('user_id', $user->id)
            ->whereDate('date', '>=', $now->copy()->subDays(6)->toDateString())
            ->whereDate('date', '<=', $now->toDateString())
            ->get();

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $dayTx = $weekTransactions->filter(fn($t) => $t->date->toDateString() === $date->toDateString());
            $chartIncome[] = $dayTx->where('type', 'income')->sum('amount');
            $chartExpense[] = $dayTx->where('type', 'expense')->sum('amount');
        }

        $announcements = \App\Models\Announcement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->get();

        return view('dashboard', compact(
            'totalBalance',
            'incomeThisMonth',
            'expenseThisMonth',
            'recentTransactions',
            'budgets',
            'budgetAlerts',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'top5Labels',
            'top5Data',
            'top5Colors',
            'announcements',
        ));
    }

    public function saveLayout(Request $request)
    {
        $request->validate(['layout' => 'required|array']);
        $user = Auth::user();
        $user->dashboard_layout = $request->layout;
        $user->save();
        return response()->json(['success' => true]);
    }
}
