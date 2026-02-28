<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderBy('deadline')
            ->get();

        $totalTarget = $goals->sum('target_amount');
        $totalSaved = $goals->sum('current_amount');

        return view('goals.index', compact('goals', 'totalTarget', 'totalSaved'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:10000',
            'deadline' => 'nullable|date|after:today',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = 0;
        $validated['icon'] = $validated['icon'] ?? 'target';

        Goal::create($validated);

        return redirect()->route('goals.index')->with('success', 'Target berhasil dibuat.');
    }

    public function addFund(Request $request, Goal $goal)
    {
        if ($goal->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $remaining = (float) $goal->target_amount - (float) $goal->current_amount;
        $addAmount = min((float) $validated['amount'], $remaining);

        $goal->current_amount = (float) $goal->current_amount + $addAmount;
        $goal->save();

        return redirect()->route('goals.index')->with('success', 'Dana berhasil ditambahkan ke target.');
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:10000',
            'deadline' => 'nullable|date',
            'color' => 'nullable|string|max:7',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index')->with('success', 'Target berhasil diperbarui.');
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id != Auth::id()) abort(403);

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Target berhasil dihapus.');
    }
}
