<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GymController extends Controller
{
    public function index()
    {
        $gyms = Gym::with(['owner', 'activeSubscription.plan'])->latest()->paginate(20);
        return view('super-admin.gyms.index', compact('gyms'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super-admin.gyms.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'owner_name'     => 'required|string|max:100',
            'owner_email'    => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
            'plan_id'        => 'required|exists:subscription_plans,id',
            'billing_cycle'  => 'required|in:monthly,annual',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'currency'       => 'nullable|string|max:10',
            'timezone'       => 'nullable|string|max:60',
        ]);

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $slug = Str::slug($data['name']);
        $origSlug = $slug;
        $i = 1;
        while (Gym::where('slug', $slug)->exists()) {
            $slug = $origSlug . '-' . $i++;
        }

        $gym = Gym::create([
            'name'              => $data['name'],
            'slug'              => $slug,
            'subscription_plan' => $plan->name,
            'status'            => 'active',
            'phone'             => $data['phone'] ?? null,
            'email'             => $data['email'] ?? null,
            'city'              => $data['city'] ?? null,
            'country'           => $data['country'] ?? 'India',
            'currency'          => $data['currency'] ?? 'INR',
            'timezone'          => $data['timezone'] ?? 'Asia/Kolkata',
        ]);

        $owner = User::create([
            'name'     => $data['owner_name'],
            'email'    => $data['owner_email'],
            'password' => Hash::make($data['owner_password']),
            'gym_id'   => $gym->id,
            'status'   => 'active',
        ]);
        $owner->assignRole('gym_owner');
        $gym->update(['owner_id' => $owner->id]);

        $amount = $data['billing_cycle'] === 'monthly' ? $plan->monthly_price : $plan->annual_price;
        GymSubscription::create([
            'gym_id'        => $gym->id,
            'plan_id'       => $plan->id,
            'status'        => 'active',
            'billing_cycle' => $data['billing_cycle'],
            'amount'        => $amount,
            'started_at'    => now(),
            'expires_at'    => $data['billing_cycle'] === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        return redirect()->route('super-admin.gyms.show', $gym)->with('success', 'Gym created and owner account set up.');
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
            'name'     => 'required|string|max:150',
            'status'   => 'required|in:active,trial,suspended,inactive',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:150',
            'city'     => 'nullable|string|max:100',
            'country'  => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:60',
        ]);
        $gym->update($data);
        return redirect()->route('super-admin.gyms.show', $gym)->with('success', 'Gym updated successfully.');
    }

    public function destroy(Gym $gym)
    {
        $gym->delete();
        return redirect()->route('super-admin.gyms.index')->with('success', 'Gym deleted.');
    }
}
