<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\GymModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GymController extends Controller
{
    public function index()
    {
        $gyms = Gym::with(['owner', 'activeSubscription.plan'])->latest()->paginate(20);
        return view('super-admin.gyms.index', compact('gyms'));
    }

    public function create()
    {
        $eligibleSubscribers = $this->eligibleSubscribers();
        $gyms = Gym::with(['owner', 'activeSubscription.plan'])->latest()->paginate(10);

        return view('super-admin.gyms.create', compact('eligibleSubscribers', 'gyms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'subscriber_id'  => 'required|exists:users,id',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'currency'       => 'nullable|string|max:10',
            'timezone'       => 'nullable|string|max:60',
        ]);

        $subscriber = User::query()
            ->with(['ownedGyms.activeSubscription.plan.modules'])
            ->findOrFail($data['subscriber_id']);

        $sourceGym = $this->subscriptionSourceGym($subscriber);

        if (!$sourceGym) {
            return back()
                ->withInput()
                ->withErrors([
                    'subscriber_id' => 'The selected subscriber does not have an active multi-gym subscription.',
                ]);
        }

        $slug = Str::slug($data['name']);
        $origSlug = $slug;
        $i = 1;
        while (Gym::where('slug', $slug)->exists()) {
            $slug = $origSlug . '-' . $i++;
        }

        $gym = Gym::create([
            'name'              => $data['name'],
            'slug'              => $slug,
            'owner_id'          => $subscriber->id,
            'subscription_plan' => $sourceGym->activeSubscription?->plan?->name ?? $sourceGym->subscription_plan,
            'status'            => 'active',
            'phone'             => $data['phone'] ?? null,
            'email'             => $data['email'] ?? null,
            'city'              => $data['city'] ?? null,
            'country'           => $data['country'] ?? 'India',
            'currency'          => $data['currency'] ?? 'INR',
            'timezone'          => $data['timezone'] ?? 'Asia/Kolkata',
        ]);

        return redirect()
            ->route('super-admin.gyms.show', $gym)
            ->with('success', 'Gym created and linked to the selected subscriber.');
    }

    public function show(Gym $gym)
    {
        $gym->load(['owner', 'subscriptions.plan']);
        $memberCount  = $gym->members()->count();
        $trainerCount = $gym->trainers()->count();
        return view('super-admin.gyms.show', compact('gym', 'memberCount', 'trainerCount'));
    }

    public function edit(Gym $gym)
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super-admin.gyms.edit', compact('gym', 'plans'));
    }

    public function update(Request $request, Gym $gym)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'status'         => 'required|in:active,trial,suspended,inactive',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'currency'       => 'nullable|string|max:10',
            'timezone'       => 'nullable|string|max:60',
            'owner_password' => 'nullable|string|min:8|confirmed',
        ]);

        $ownerPassword = $data['owner_password'] ?? null;
        unset($data['owner_password']);

        $gym->update($data);

        if (!empty($ownerPassword) && $gym->owner) {
            $gym->owner->update([
                'password' => Hash::make($ownerPassword),
            ]);
        }

        return redirect()->route('super-admin.gyms.show', $gym)->with('success', 'Gym updated successfully.');
    }

    public function destroy(Gym $gym)
    {
        $gym->delete();
        return redirect()->route('super-admin.gyms.index')->with('success', 'Gym deleted.');
    }

    private function eligibleSubscribers()
    {
        return User::query()
            ->role('gym_owner')
            ->with(['ownedGyms.activeSubscription.plan.modules'])
            ->get()
            ->map(function (User $subscriber) {
                $sourceGym = $this->subscriptionSourceGym($subscriber);

                if (!$sourceGym) {
                    return null;
                }

                $subscriber->setRelation(
                    'ownedGyms',
                    $subscriber->ownedGyms->sortByDesc('created_at')->values()
                );

                $subscriber->source_gym_id = $sourceGym->id;
                $subscriber->source_plan_name = $sourceGym->activeSubscription?->plan?->display_name ?? ucfirst((string) $sourceGym->subscription_plan);
                $subscriber->gym_count = $subscriber->ownedGyms->count();

                return $subscriber;
            })
            ->filter()
            ->sortBy(fn (User $subscriber) => strtolower($subscriber->name))
            ->values();
    }

    private function subscriptionSourceGym(User $subscriber): ?Gym
    {
        return $subscriber->ownedGyms
            ->sortByDesc(function (Gym $gym) {
                return optional($gym->activeSubscription?->started_at)->timestamp ?? $gym->created_at?->timestamp ?? 0;
            })
            ->first(function (Gym $gym) {
                return $gym->activeSubscription
                    && in_array('multi_branch', GymModuleRegistry::gymKeys($gym), true);
            });
    }
}
