<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecurringController extends Controller
{
    public function index()
    {
        $recurring = RecurringTransaction::where('user_id', Auth::id())
            ->with(['account', 'category'])
            ->orderBy('next_due')
            ->get();

        $accounts = Account::where('user_id', Auth::id())->where('is_active', true)->get();
        $categories = Category::forUser(Auth::id())->get();

        return view('recurring.index', compact('recurring', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['next_due'] = $validated['start_date'];

        $rec = RecurringTransaction::create($validated);
        ActivityLog::log('created', $rec, "Transaksi berulang '{$validated['description']}' dibuat.");

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang berhasil dibuat.');
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        if ($recurring->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'end_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ]);

        $recurring->update($validated);
        ActivityLog::log('updated', $recurring, "Transaksi berulang diperbarui.");

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang diperbarui.');
    }

    public function toggle(RecurringTransaction $recurring)
    {
        if ($recurring->user_id != Auth::id()) abort(403);

        $recurring->is_active = !$recurring->is_active;
        $recurring->save();

        $status = $recurring->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('recurring.index')->with('success', "Transaksi berulang {$status}.");
    }

    public function destroy(RecurringTransaction $recurring)
    {
        if ($recurring->user_id != Auth::id()) abort(403);

        ActivityLog::log('deleted', $recurring, "Transaksi berulang dihapus.");
        $recurring->delete();

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang dihapus.');
    }

    public function processRecurring()
    {
        $today = now()->toDateString();
        $due = RecurringTransaction::where('is_active', true)
            ->whereDate('next_due', '<=', $today)
            ->with(['account'])
            ->get();

        $count = 0;
        foreach ($due as $rec) {
            if ($rec->end_date && $rec->next_due->gt($rec->end_date)) {
                $rec->update(['is_active' => false]);
                continue;
            }

            DB::transaction(function () use ($rec) {
                Transaction::create([
                    'user_id' => $rec->user_id,
                    'account_id' => $rec->account_id,
                    'category_id' => $rec->category_id,
                    'type' => $rec->type,
                    'amount' => $rec->amount,
                    'description' => $rec->description . ' (otomatis)',
                    'date' => $rec->next_due->toDateString(),
                    'tags' => ['recurring'],
                ]);

                $account = $rec->account;
                if ($rec->type === 'income') {
                    $account->balance = (float) $account->balance + (float) $rec->amount;
                } else {
                    $account->balance = (float) $account->balance - (float) $rec->amount;
                }
                $account->save();

                $rec->next_due = $rec->calculateNextDue();
                $rec->save();
            });

            $count++;
        }

        return $count;
    }
}
