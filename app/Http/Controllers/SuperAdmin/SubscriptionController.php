<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\GymModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'subscription_status' => (string) $request->query('subscription_status', ''),
            'gym_status' => (string) $request->query('gym_status', ''),
            'billing_cycle' => (string) $request->query('billing_cycle', ''),
            'plan_id' => (string) $request->query('plan_id', ''),
        ];

        $subscriberRows = $this->subscriberRows($filters);
        $subscribers = $this->paginateCollection($subscriberRows, 12, $request);

        $statusCounts = GymSubscription::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $overview = [
            'total_subscribers' => $subscriberRows->count(),
            'active_subscribers' => $subscriberRows->where('subscription_status', 'active')->count(),
            'multi_gym_subscribers' => $subscriberRows->where('gym_count', '>', 1)->count(),
            'total_gyms' => $subscriberRows->sum('gym_count'),
            'active_gyms' => $subscriberRows->sum('active_gyms_count'),
            'monthly_revenue' => (float) $subscriberRows
                ->sum(fn (array $row) => $row['billing_cycle'] === 'monthly' && $row['subscription_status'] === 'active'
                    ? (float) $row['amount']
                    : 0),
            'raw_subscription_total' => GymSubscription::count(),
            'raw_active_subscriptions' => (int) ($statusCounts['active'] ?? 0),
        ];

        $plans = SubscriptionPlan::query()
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get(['id', 'display_name']);

        return view('super-admin.subscriptions.index', compact('subscribers', 'overview', 'filters', 'plans'));
    }

    public function show(User $subscriber)
    {
        abort_unless($subscriber->hasRole('gym_owner'), 404);

        $subscriber->load([
            'ownedGyms' => function ($query) {
                $query->withCount(['members', 'trainers'])
                    ->with([
                        'owner',
                        'activeSubscription.plan.modules',
                        'subscriptions.plan',
                    ]);
            },
        ]);

        $subscriptionGym = $this->subscriptionGymFor($subscriber);
        abort_unless($subscriptionGym !== null, 404);

        $ownedGyms = $subscriber->ownedGyms
            ->sortByDesc('created_at')
            ->values();

        $totals = [
            'gym_count' => $ownedGyms->count(),
            'active_gyms_count' => $ownedGyms->where('status', 'active')->count(),
            'members_count' => $ownedGyms->sum('members_count'),
            'trainers_count' => $ownedGyms->sum('trainers_count'),
        ];

        return view('super-admin.subscriptions.show', [
            'subscriber' => $subscriber,
            'subscriptionGym' => $subscriptionGym,
            'ownedGyms' => $ownedGyms,
            'totals' => $totals,
        ]);
    }

    private function subscriberRows(array $filters): Collection
    {
        return User::query()
            ->role('gym_owner')
            ->whereHas('ownedGyms.subscriptions')
            ->with([
                'ownedGyms' => function ($query) {
                    $query->with([
                        'activeSubscription.plan.modules',
                        'subscriptions' => fn ($subscriptionQuery) => $subscriptionQuery->with('plan')->latest(),
                    ])->withCount([
                        'members',
                        'trainers',
                        'workoutLogs',
                        'whatsappLogs',
                        'loginHistories',
                    ]);
                },
            ])
            ->get()
            ->map(function (User $subscriber) {
                $subscriptionGym = $this->subscriptionGymFor($subscriber);

                if (!$subscriptionGym) {
                    return null;
                }

                $currentSubscription = $subscriptionGym->activeSubscription ?: $subscriptionGym->subscriptions->first();
                $plan = $currentSubscription?->plan;
                $ownedGyms = $subscriber->ownedGyms->sortByDesc('created_at')->values();

                return [
                    'subscriber' => $subscriber,
                    'subscription_gym' => $subscriptionGym,
                    'current_subscription' => $currentSubscription,
                    'plan' => $plan,
                    'subscription_status' => $currentSubscription?->status ?? 'inactive',
                    'billing_cycle' => $currentSubscription?->billing_cycle,
                    'amount' => (float) ($currentSubscription?->amount ?? 0),
                    'gym_count' => $ownedGyms->count(),
                    'active_gyms_count' => $ownedGyms->where('status', 'active')->count(),
                    'members_count' => $ownedGyms->sum('members_count'),
                    'trainers_count' => $ownedGyms->sum('trainers_count'),
                    'workout_logs_count' => $ownedGyms->sum('workout_logs_count'),
                    'whatsapp_logs_count' => $ownedGyms->sum('whatsapp_logs_count'),
                    'login_histories_count' => $ownedGyms->sum('login_histories_count'),
                    'has_multi_branch' => $ownedGyms->contains(fn (Gym $gym) => in_array('multi_branch', GymModuleRegistry::gymKeys($gym), true)),
                    'owned_gyms' => $ownedGyms,
                ];
            })
            ->filter()
            ->filter(function (array $row) use ($filters) {
                $subscriber = $row['subscriber'];
                $subscriptionGym = $row['subscription_gym'];

                if ($filters['search'] !== '') {
                    $search = strtolower($filters['search']);
                    $matchesSubscriber = str_contains(strtolower($subscriber->name), $search)
                        || str_contains(strtolower($subscriber->email), $search)
                        || str_contains(strtolower((string) $subscriber->phone), $search);
                    $matchesGym = $row['owned_gyms']->contains(function (Gym $gym) use ($search) {
                        return str_contains(strtolower($gym->name), $search)
                            || str_contains(strtolower((string) $gym->email), $search)
                            || str_contains(strtolower((string) $gym->city), $search);
                    });

                    if (!$matchesSubscriber && !$matchesGym) {
                        return false;
                    }
                }

                if ($filters['subscription_status'] !== '') {
                    if ($filters['subscription_status'] === 'inactive') {
                        if (!in_array($row['subscription_status'], ['expired', 'cancelled', 'inactive'], true)) {
                            return false;
                        }
                    } elseif ($row['subscription_status'] !== $filters['subscription_status']) {
                        return false;
                    }
                }

                if ($filters['gym_status'] !== '' && !$row['owned_gyms']->contains(fn (Gym $gym) => $gym->status === $filters['gym_status'])) {
                    return false;
                }

                if ($filters['billing_cycle'] !== '' && $row['billing_cycle'] !== $filters['billing_cycle']) {
                    return false;
                }

                if ($filters['plan_id'] !== '' && (string) $subscriptionGym->activeSubscription?->plan_id !== $filters['plan_id']) {
                    $fallbackPlanId = (string) optional($row['current_subscription'])->plan_id;

                    if ($fallbackPlanId !== $filters['plan_id']) {
                        return false;
                    }
                }

                return true;
            })
            ->sortByDesc(function (array $row) {
                $subscription = $row['current_subscription'];

                return optional($subscription?->started_at)->timestamp
                    ?? optional($row['subscription_gym']->created_at)->timestamp
                    ?? 0;
            })
            ->values();
    }

    private function subscriptionGymFor(User $subscriber): ?Gym
    {
        return $subscriber->ownedGyms
            ->sortByDesc(function (Gym $gym) {
                return optional($gym->activeSubscription?->started_at)->timestamp ?? $gym->created_at?->timestamp ?? 0;
            })
            ->first(function (Gym $gym) {
                return $gym->activeSubscription !== null;
            }) ?: $subscriber->ownedGyms
            ->sortByDesc('created_at')
            ->first(function (Gym $gym) {
                return $gym->subscriptions->isNotEmpty();
            });
    }

    private function paginateCollection(Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $pageItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
