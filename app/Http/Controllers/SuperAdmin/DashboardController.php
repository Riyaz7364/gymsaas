<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeSubscriptions  = GymSubscription::where('status', 'active')->count();
        $trialSubscriptions   = GymSubscription::where('status', 'trial')->count();
        $expiredSubscriptions = GymSubscription::whereIn('status', ['expired', 'cancelled'])->count();

        $expiringIn7Days = GymSubscription::whereIn('status', ['active', 'trial'])
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>=', now())
            ->count();

        $mrrMonthly = (float) GymSubscription::where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount');
        $mrrAnnual  = (float) GymSubscription::where('status', 'active')->where('billing_cycle', 'annual')->sum(DB::raw('amount / 12'));
        $mrr = $mrrMonthly + $mrrAnnual;
        $arr = $mrr * 12;

        $revenueLastMonth = (float) GymSubscription::where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->where('started_at', '<=', now()->subMonth()->endOfMonth())
            ->sum('amount');
        $revenueGrowth = $revenueLastMonth > 0
            ? round((($mrr - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($mrr > 0 ? 100 : 0);

        $monthlyRevenue = [];
        $monthlyLabels  = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M');
            $rev = GymSubscription::where('billing_cycle', 'monthly')
                ->whereIn('status', ['active', 'expired', 'cancelled'])
                ->where('started_at', '<=', $month->copy()->endOfMonth())
                ->where(function ($q) use ($month) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', $month->copy()->startOfMonth());
                })
                ->sum('amount');
            $monthlyRevenue[] = (float) $rev;
        }

        $planStats = SubscriptionPlan::withCount(['activeSubscriptions as active_count'])->get();

        $gymGrowthData   = [];
        $gymGrowthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $gymGrowthLabels[] = $month->format('M');
            $gymGrowthData[]   = Gym::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)->count();
        }

        $recentSubscriptions = GymSubscription::with(['gym', 'plan'])->latest()->take(8)->get();
        $totalGyms = Gym::count();

        return view('super-admin.dashboard', compact(
            'activeSubscriptions', 'trialSubscriptions', 'expiredSubscriptions', 'expiringIn7Days',
            'mrr', 'arr', 'revenueGrowth',
            'monthlyRevenue', 'monthlyLabels',
            'planStats', 'gymGrowthData', 'gymGrowthLabels',
            'recentSubscriptions', 'totalGyms'
        ));
    }
}