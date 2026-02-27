<?php

namespace App\Http\Controllers;

use App\Models\TransactionTemplate;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = TransactionTemplate::where('user_id', Auth::id())
            ->with(['account', 'category'])
            ->orderBy('name')
            ->get();

        $accounts = Account::where('user_id', Auth::id())->where('is_active', true)->get();
        $categories = Category::forUser(Auth::id())->get();

        return view('templates.index', compact('templates', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['icon'] = $validated['icon'] ?? 'zap';
        $validated['color'] = $validated['color'] ?? '#0F766E';

        TransactionTemplate::create($validated);

        return redirect()->route('templates.index')->with('success', 'Template transaksi berhasil dibuat.');
    }

    public function use(TransactionTemplate $template)
    {
        if ($template->user_id !== Auth::id()) abort(403);

        $account = $template->account;
        if (!$account) {
            return redirect()->route('templates.index')->with('error', 'Akun tidak ditemukan.');
        }

        DB::transaction(function () use ($template, $account) {
            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $template->account_id,
                'category_id' => $template->category_id,
                'type' => $template->type,
                'amount' => $template->amount,
                'description' => $template->description,
                'date' => now()->toDateString(),
                'tags' => $template->tags,
            ]);

            if ($template->type === 'income') {
                $account->balance = (float) $account->balance + (float) $template->amount;
            } else {
                $account->balance = (float) $account->balance - (float) $template->amount;
            }
            $account->save();
        });

        ActivityLog::log('created', $template, "Transaksi dari template '{$template->name}'.");

        return redirect()->route('transactions.index')->with('success', "Transaksi dari template '{$template->name}' berhasil dicatat.");
    }

    public function destroy(TransactionTemplate $template)
    {
        if ($template->user_id !== Auth::id()) abort(403);

        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }
}
