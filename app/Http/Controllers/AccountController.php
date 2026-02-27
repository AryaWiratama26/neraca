<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalBalance = $accounts->where('is_active', true)->sum('balance');

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,ewallet,savings',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['icon'] = match($validated['type']) {
            'cash' => 'wallet',
            'bank' => 'landmark',
            'ewallet' => 'smartphone',
            'savings' => 'piggy-bank',
        };

        Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,ewallet,savings',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['icon'] = match($validated['type']) {
            'cash' => 'wallet',
            'bank' => 'landmark',
            'ewallet' => 'smartphone',
            'savings' => 'piggy-bank',
        };

        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) abort(403);

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus.');
    }
}
