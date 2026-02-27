<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PredictionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Get last 3 months of transactions for prediction
        $threeMonthsAgo = $now->copy()->subMonths(3)->startOfMonth();
        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereDate('date', '>=', $threeMonthsAgo->toDateString())
            ->whereDate('date', '<', $now->copy()->startOfMonth()->toDateString())
            ->with('category')
            ->get();

        $monthCount = 3;

        // Total monthly averages
        $avgExpense = $transactions->sum('amount') / max($monthCount, 1);
        $avgIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereDate('date', '>=', $threeMonthsAgo->toDateString())
            ->whereDate('date', '<', $now->copy()->startOfMonth()->toDateString())
            ->get()
            ->sum('amount') / max($monthCount, 1);

        // Category breakdown prediction
        $categoryPredictions = $transactions
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->map(function ($group) use ($monthCount) {
                return (object) [
                    'category' => $group->first()->category,
                    'avg_monthly' => $group->sum('amount') / max($monthCount, 1),
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('avg_monthly')
            ->values();

        // Current month progress
        $currentMonthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->get()
            ->sum('amount');

        $daysInMonth = $now->daysInMonth;
        $dayOfMonth = $now->day;
        $projectedExpense = ($dayOfMonth > 0) ? ($currentMonthExpense / $dayOfMonth) * $daysInMonth : 0;

        // Monthly trend for chart (6 months)
        $trendLabels = [];
        $trendExpense = [];
        $trendIncome = [];
        $sixMonthTx = Transaction::where('user_id', $user->id)
            ->whereDate('date', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->get();

        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $trendLabels[] = $m->translatedFormat('M');
            $monthlyTx = $sixMonthTx->filter(fn($t) => $t->date->month === $m->month && $t->date->year === $m->year);
            $trendExpense[] = (float) $monthlyTx->where('type', 'expense')->sum('amount');
            $trendIncome[] = (float) $monthlyTx->where('type', 'income')->sum('amount');
        }

        // Add prediction month
        $nextMonth = $now->copy()->addMonth();
        $trendLabels[] = $nextMonth->translatedFormat('M') . ' (est)';
        $trendExpense[] = round($avgExpense);
        $trendIncome[] = round($avgIncome);

        // Chart data for category predictions
        $predLabels = $categoryPredictions->take(8)->map(fn($p) => $p->category->name ?? 'Lainnya')->toArray();
        $predData = $categoryPredictions->take(8)->pluck('avg_monthly')->map(fn($v) => round($v))->toArray();
        $predColors = $categoryPredictions->take(8)->map(fn($p) => $p->category->color ?? '#6B7280')->toArray();

        return view('prediction.index', compact(
            'avgExpense', 'avgIncome', 'currentMonthExpense', 'projectedExpense',
            'categoryPredictions', 'trendLabels', 'trendExpense', 'trendIncome',
            'predLabels', 'predData', 'predColors', 'now', 'nextMonth'
        ));
    }
}
