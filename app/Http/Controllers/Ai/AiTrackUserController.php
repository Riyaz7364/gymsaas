<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Trainer;
use App\Services\AiUserTrackingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AiTrackUserController extends Controller
{
    public function index(Request $request, AiUserTrackingService $trackingService)
    {
        $gymId = auth()->user()->gym_id;

        $members = Member::query()
            ->where('gym_id', $gymId)
            ->with(['activePlan', 'trainer'])
            ->withMax('attendances as last_check_in', 'check_in')
            ->withCount([
                'attendances as attendance_30_count' => fn ($q) => $q->where('check_in', '>=', now()->subDays(30)),
                'attendances as attendance_prev_30_count' => fn ($q) => $q
                    ->where('check_in', '>=', now()->subDays(60))
                    ->where('check_in', '<', now()->subDays(30)),
                'invoices as overdue_invoices_count' => fn ($q) => $q
                    ->whereIn('status', ['overdue', 'unpaid'])
                    ->whereDate('due_date', '<', now()->toDateString()),
            ])
            ->withSum([
                'invoices as pending_balance_sum' => fn ($q) => $q
                    ->whereIn('status', ['partial', 'overdue', 'unpaid']),
            ], 'balance_due')
            ->get();

        $attendancePatternByMember = Attendance::query()
            ->where('gym_id', $gymId)
            ->where('check_in', '>=', now()->subDays(90))
            ->selectRaw('member_id, HOUR(check_in) as hour_of_day, DAYOFWEEK(check_in) as day_idx, COUNT(*) as visits')
            ->groupBy('member_id', 'hour_of_day', 'day_idx')
            ->get()
            ->groupBy('member_id');

        $insights = $members->map(function (Member $member) use ($trackingService, $attendancePatternByMember) {
            return $trackingService->evaluate($member, $attendancePatternByMember->get($member->id, collect()));
        });

        if ($search = trim((string) $request->get('search'))) {
            $insights = $insights->filter(function (array $row) use ($search) {
                $member = $row['member'];
                return str_contains(strtolower($member->name ?? ''), strtolower($search))
                    || str_contains(strtolower($member->phone ?? ''), strtolower($search))
                    || str_contains(strtolower($member->member_no ?? ''), strtolower($search));
            });
        }

        if ($risk = $request->get('risk')) {
            $insights = $insights->where('risk_level', $risk);
        }

        $insights = $insights->sortByDesc('risk_score')->values();

        $perPage = 20;
        $page = max(1, (int) $request->get('page', 1));
        $itemsForCurrentPage = $insights->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedInsights = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $insights->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $summary = [
            'total' => $insights->count(),
            'high' => $insights->where('risk_level', 'high')->count(),
            'medium' => $insights->where('risk_level', 'medium')->count(),
            'low' => $insights->where('risk_level', 'low')->count(),
            'offer_candidates' => $insights->where('can_send_offer', true)->count(),
        ];

        $topTrainerQuery = Trainer::query()
            ->where('gym_id', $gymId)
            ->withCount('members');

        if (Schema::hasTable('trainer_reviews')) {
            $topTrainerQuery->withAvg('reviews', 'rating');
        }

        $topTrainer = $topTrainerQuery
            ->orderByDesc('members_count')
            ->first();

        return view('ai.track-user.index', [
            'insights' => $paginatedInsights,
            'summary' => $summary,
            'topTrainer' => $topTrainer,
        ]);
    }
}
