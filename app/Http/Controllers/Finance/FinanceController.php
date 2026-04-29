<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Gym $gym)
    {
        // Verify the user belongs to this gym
        if (auth()->user()->gym_id !== $gym->id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Get financial data
        $totalRevenue = Payment::where('gym_id', $gym->id)
            ->where('status', 'success')
            ->sum('amount');

        $totalExpenses = Expense::where('gym_id', $gym->id)
            ->sum('amount');

        $pendingPayments = Payment::where('gym_id', $gym->id)
            ->where('status', 'pending')
            ->sum('amount');

        $unpaidInvoices = Invoice::where('gym_id', $gym->id)
            ->where('status', '!=', 'paid')
            ->sum('balance_due');

        $recentPayments = Payment::where('gym_id', $gym->id)
            ->with(['member', 'invoice'])
            ->latest()
            ->limit(5)
            ->get();

        $recentExpenses = Expense::where('gym_id', $gym->id)
            ->with('expenseType')
            ->latest()
            ->limit(5)
            ->get();

        $monthlyRevenue = Payment::where('gym_id', $gym->id)
            ->where('status', 'success')
            ->whereYear('paid_at', now()->year)
            ->selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->month => $item->total]);

        $invoiceStats = [
            'total' => Invoice::where('gym_id', $gym->id)->count(),
            'paid' => Invoice::where('gym_id', $gym->id)->where('status', 'paid')->count(),
            'unpaid' => Invoice::where('gym_id', $gym->id)->where('status', 'unpaid')->count(),
            'partial' => Invoice::where('gym_id', $gym->id)->where('status', 'partial')->count(),
        ];

        return view('gym.finance.index', compact(
            'gym',
            'totalRevenue',
            'totalExpenses',
            'pendingPayments',
            'unpaidInvoices',
            'recentPayments',
            'recentExpenses',
            'monthlyRevenue',
            'invoiceStats'
        ));
    }
}

``````````
