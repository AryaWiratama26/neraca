<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\RecurringTransaction;
use App\Models\TransactionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('activity.index', compact('logs'));
    }
}
