<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category', 'account']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('tag')) {
            $tagSearch = strtolower(trim($request->tag));
            $query->where(function($q) use ($tagSearch) {
                $q->whereRaw("LOWER(tags) LIKE ?", ["%\"$tagSearch\"%"]);
            });
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $accounts = Account::where('user_id', Auth::id())->where('is_active', true)->get();
        $categories = Category::forUser(Auth::id())->get();

        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
            'tags' => 'nullable|string|max:255',
        ]);

        // Parse tags from comma-separated string
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            $validated['tags'] = array_filter($validated['tags']);
        } else {
            $validated['tags'] = null;
        }

        DB::transaction(function () use ($validated) {
            $account = Account::where('id', $validated['account_id'])
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            $validated['user_id'] = Auth::id();
            Transaction::create($validated);

            
            if ($validated['type'] === 'income') {
                $account->balance = (float) $account->balance + (float) $validated['amount'];
            } else {
                $account->balance = (float) $account->balance - (float) $validated['amount'];
            }
            $account->save();

            
            if ($validated['type'] === 'expense') {
                $date = \Carbon\Carbon::parse($validated['date']);
                $budget = \App\Models\Budget::where('user_id', Auth::id())
                    ->where('category_id', $validated['category_id'])
                    ->where('month', $date->month)
                    ->where('year', $date->year)
                    ->lockForUpdate()
                    ->first();

                if ($budget) {
                    $budget->spent = (float) $budget->spent + (float) $validated['amount'];
                    $budget->save();
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
            'tags' => 'nullable|string|max:255',
        ]);

        // Parse tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        } else {
            $validated['tags'] = null;
        }

        DB::transaction(function () use ($transaction, $validated) {
            $oldAccountId = $transaction->account_id;
            $newAccountId = $validated['account_id'];

            // Prevent deadlocks by ordering IDs
            $accountIds = array_unique([$oldAccountId, $newAccountId]);
            sort($accountIds);
            
            $accounts = Account::whereIn('id', $accountIds)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $oldAccount = $accounts->get($oldAccountId);
            $newAccount = $accounts->get($newAccountId);

            if (!$newAccount) {
                abort(404, 'Account not found');
            }

            // Reverse old transaction
            if ($oldAccount) {
                if ($transaction->type === 'income') {
                    $oldAccount->balance = (float) $oldAccount->balance - (float) $transaction->amount;
                } else {
                    $oldAccount->balance = (float) $oldAccount->balance + (float) $transaction->amount;
                }
                $oldAccount->save();
            }

            
            if ($oldAccount && $oldAccount->id === $newAccount->id) {
                $newAccount = $oldAccount;
            }
            
            if ($validated['type'] === 'income') {
                $newAccount->balance = (float) $newAccount->balance + (float) $validated['amount'];
            } else {
                $newAccount->balance = (float) $newAccount->balance - (float) $validated['amount'];
            }
            $newAccount->save();

            $transaction->update($validated);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {
            $accountIds = [$validated['from_account_id'], $validated['to_account_id']];
            sort($accountIds); // Sort to prevent deadlocks

            $accounts = Account::whereIn('id', $accountIds)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $fromAccount = $accounts->get($validated['from_account_id']);
            $toAccount = $accounts->get($validated['to_account_id']);

            if (!$fromAccount || !$toAccount) abort(404, 'Account not found');

            $fromAccount->balance = (float) $fromAccount->balance - (float) $validated['amount'];
            $fromAccount->save();

            $toAccount->balance = (float) $toAccount->balance + (float) $validated['amount'];
            $toAccount->save();

            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $fromAccount->id,
                'category_id' => null,
                'type' => 'expense',
                'amount' => $validated['amount'],
                'description' => 'Transfer ke ' . $toAccount->name,
                'date' => $validated['date'],
                'tags' => ['transfer'],
            ]);

            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $toAccount->id,
                'category_id' => null,
                'type' => 'income',
                'amount' => $validated['amount'],
                'description' => 'Transfer dari ' . $fromAccount->name,
                'date' => $validated['date'],
                'tags' => ['transfer'],
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Transfer berhasil dilakukan.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        DB::transaction(function () use ($transaction) {
            $account = Account::where('id', $transaction->account_id)
                ->lockForUpdate()
                ->first();

            if ($account) {
                if ($transaction->type === 'income') {
                    $account->balance = (float) $account->balance - (float) $transaction->amount;
                } else {
                    $account->balance = (float) $account->balance + (float) $transaction->amount;
                }
                $account->save();
            }
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
