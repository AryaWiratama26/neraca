<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\RecurringTransaction;
use App\Models\TransactionTemplate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function export()
    {
        $user = Auth::user();

        $data = [
            'exported_at' => now()->toISOString(),
            'app' => 'Neraca',
            'version' => '1.0',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'accounts' => Account::where('user_id', $user->id)->get()->map(fn($a) => [
                'name' => $a->name,
                'type' => $a->type,
                'balance' => $a->balance,
                'icon' => $a->icon,
                'color' => $a->color,
                'is_active' => $a->is_active,
            ])->toArray(),
            'transactions' => Transaction::where('user_id', $user->id)->with(['category', 'account'])->get()->map(fn($t) => [
                'account_name' => $t->account->name ?? null,
                'category_name' => $t->category->name ?? null,
                'type' => $t->type,
                'amount' => $t->amount,
                'description' => $t->description,
                'date' => $t->date->toDateString(),
                'tags' => $t->tags,
            ])->toArray(),
            'budgets' => Budget::where('user_id', $user->id)->with('category')->get()->map(fn($b) => [
                'category_name' => $b->category->name ?? null,
                'amount' => $b->amount,
                'month' => $b->month,
                'year' => $b->year,
                'spent' => $b->spent,
            ])->toArray(),
            'goals' => Goal::where('user_id', $user->id)->get()->map(fn($g) => [
                'name' => $g->name,
                'target_amount' => $g->target_amount,
                'current_amount' => $g->current_amount,
                'deadline' => $g->deadline?->toDateString(),
                'icon' => $g->icon,
                'color' => $g->color,
            ])->toArray(),
            'recurring' => RecurringTransaction::where('user_id', $user->id)->with(['category', 'account'])->get()->map(fn($r) => [
                'account_name' => $r->account->name ?? null,
                'category_name' => $r->category->name ?? null,
                'type' => $r->type,
                'amount' => $r->amount,
                'description' => $r->description,
                'frequency' => $r->frequency,
                'start_date' => $r->start_date->toDateString(),
                'next_due' => $r->next_due->toDateString(),
                'end_date' => $r->end_date?->toDateString(),
                'is_active' => $r->is_active,
            ])->toArray(),
            'templates' => TransactionTemplate::where('user_id', $user->id)->with(['category', 'account'])->get()->map(fn($t) => [
                'name' => $t->name,
                'account_name' => $t->account->name ?? null,
                'category_name' => $t->category->name ?? null,
                'type' => $t->type,
                'amount' => $t->amount,
                'description' => $t->description,
                'tags' => $t->tags,
                'icon' => $t->icon,
                'color' => $t->color,
            ])->toArray(),
        ];

        ActivityLog::log('export', Auth::user(), 'Full backup exported.');

        $filename = 'neraca-backup-' . now()->format('Y-m-d-His') . '.json';
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function importForm()
    {
        return view('backup.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:json,txt|max:10240',
        ]);

        $json = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($json, true);

        if (!$data || !isset($data['app']) || $data['app'] !== 'Neraca') {
            return redirect()->back()->with('error', 'File backup tidak valid.');
        }

        $userId = Auth::id();
        $imported = ['accounts' => 0, 'transactions' => 0, 'budgets' => 0, 'goals' => 0];

        DB::transaction(function () use ($data, $userId, &$imported) {
            // Import accounts
            foreach ($data['accounts'] ?? [] as $acc) {
                $exists = Account::where('user_id', $userId)->get()->first(fn($a) => $a->name === $acc['name']);
                if (!$exists) {
                    Account::create(array_merge($acc, ['user_id' => $userId]));
                    $imported['accounts']++;
                }
            }

            // Import transactions
            $accounts = Account::where('user_id', $userId)->get();
            $categories = \App\Models\Category::forUser($userId)->get();

            foreach ($data['transactions'] ?? [] as $tx) {
                $account = $accounts->first(fn($a) => $a->name === $tx['account_name']);
                $category = $categories->first(fn($c) => $c->name === $tx['category_name']);
                if ($account) {
                    Transaction::create([
                        'user_id' => $userId,
                        'account_id' => $account->id,
                        'category_id' => $category?->id,
                        'type' => $tx['type'],
                        'amount' => $tx['amount'],
                        'description' => $tx['description'],
                        'date' => $tx['date'],
                        'tags' => $tx['tags'],
                    ]);
                    $imported['transactions']++;
                }
            }

            // Import goals
            foreach ($data['goals'] ?? [] as $goal) {
                Goal::create(array_merge($goal, ['user_id' => $userId]));
                $imported['goals']++;
            }
        });

        ActivityLog::log('import', Auth::user(), 'Backup restored.');

        $msg = "Berhasil diimpor: {$imported['accounts']} akun, {$imported['transactions']} transaksi, {$imported['goals']} target.";
        return redirect()->route('profile.index')->with('success', $msg);
    }
}
