<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\MemberPlan;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Trainer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Super admin has no gym_id — redirect to super-admin dashboard
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        $gymId = auth()->user()->gym_id;

        // Current month revenue
        $monthlyRevenue = Payment::where('gym_id', $gymId)
            ->where('status', 'success')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        // Previous month revenue for % change
        $prevRevenue = Payment::where('gym_id', $gymId)
            ->where('status', 'success')
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->sum('amount');

        $revenueChange = $prevRevenue > 0
            ? round((($monthlyRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        // Total members + change
        $totalMembers = Member::where('gym_id', $gymId)->count();
        $prevMembers  = Member::where('gym_id', $gymId)
            ->where('created_at', '<', now()->startOfMonth())
            ->count();
        $membersChange = $prevMembers > 0
            ? round((($totalMembers - $prevMembers) / $prevMembers) * 100, 1)
            : 0;

        $stats = [
            'total_members'   => $totalMembers,
            'members_change'  => $membersChange,
            'today_attendance'=> Attendance::where('gym_id', $gymId)->whereDate('check_in', today())->count(),
            'monthly_revenue' => $monthlyRevenue,
            'revenue_change'  => $revenueChange,
            'active_trainers' => Trainer::where('gym_id', $gymId)->where('status', 'active')->count(),
            'expiring_soon'   => MemberPlan::where('gym_id', $gymId)
                                    ->where('status', 'active')
                                    ->whereBetween('end_date', [today(), today()->addDays(7)])
                                    ->count(),
            'classes_today'   => GymClass::where('gym_id', $gymId)
                                    ->where('status', 'active')
                                    ->whereJsonContains('schedule_days', strtolower(now()->format('D')))
                                    ->count(),
        ];

        // Last 12 months revenue for chart
        $monthlyRevenueData = [];
        $monthlyLabels      = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyLabels[]      = $m->format('M');
            $monthlyRevenueData[] = (float) Payment::where('gym_id', $gymId)
                ->where('status', 'success')
                ->whereYear('paid_at', $m->year)
                ->whereMonth('paid_at', $m->month)
                ->sum('amount');
        }

        // Member status counts for donut chart
        $memberStatusCounts = [
            'active'   => Member::where('gym_id', $gymId)->where('status', 'active')->count(),
            'frozen'   => Member::where('gym_id', $gymId)->where('status', 'frozen')->count(),
            'expired'  => Member::where('gym_id', $gymId)->where('status', 'expired')->count(),
            'inactive' => Member::where('gym_id', $gymId)->where('status', 'inactive')->count(),
        ];

        // Recent members
        $recentMembers = Member::where('gym_id', $gymId)
            ->latest()
            ->limit(6)
            ->get();

        // Today attendance
        $todayAttendance = Attendance::where('gym_id', $gymId)
            ->whereDate('check_in', today())
            ->with('member')
            ->latest('check_in')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'stats',
            'monthlyRevenueData',
            'monthlyLabels',
            'memberStatusCounts',
            'recentMembers',
            'todayAttendance'
        ));
    }
}
