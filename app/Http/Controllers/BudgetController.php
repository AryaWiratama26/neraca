<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $budgets = Budget::where('user_id', Auth::id())
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->with('category')
            ->get();

        // Recalculate spent from actual transactions (PHP-level sum)
        foreach ($budgets as $budget) {
            $actualSpent = Transaction::where('user_id', Auth::id())
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->get()
                ->sum('amount');

            if ((float) $budget->spent !== (float) $actualSpent) {
                $budget->spent = $actualSpent;
                $budget->save();
            }
        }

        $categories = Category::forUser(Auth::id())
            ->where('type', 'expense')
            ->get();

        $budgetedCategoryIds = $budgets->pluck('category_id')->toArray();
        $availableCategories = $categories->whereNotIn('id', $budgetedCategoryIds);

        return view('budgets.index', compact('budgets', 'availableCategories', 'now'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $now = Carbon::now();

        $exists = Budget::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->exists();

        if ($exists) {
            return redirect()->route('budgets.index')->with('error', 'Budget untuk kategori ini sudah ada bulan ini.');
        }

        // Calculate current spent (PHP-level sum)
        $spent = Transaction::where('user_id', Auth::id())
            ->where('category_id', $validated['category_id'])
            ->where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->get()
            ->sum('amount');

        Budget::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'month' => $now->month,
            'year' => $now->year,
            'spent' => $spent,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Budget $budget)
    {
        if ($budget->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id != Auth::id()) abort(403);

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil dihapus.');
    }
}
