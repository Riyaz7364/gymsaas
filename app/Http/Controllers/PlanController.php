<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $plans = Plan::withCount('memberPlans')
            ->where('gym_id', $gym->id)
            ->orderBy('price')
            ->get();
        
        return view('plans.index', compact(['plans', 'gym']));
    }

    public function create()
    {
        return view('plans.create');
    }

    private array $presetDays = [
        'monthly'    => 30,
        'quarterly'  => 90,
        'half_yearly'=> 180,
        'yearly'     => 365,
    ];

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'type'         => ['required', 'in:monthly,quarterly,half_yearly,yearly,custom'],
            'duration_days'=> ['required_if:type,custom', 'nullable', 'integer', 'min:1'],
            'price'        => ['required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $data['duration_days'] = $data['type'] === 'custom'
            ? $data['duration_days']
            : $this->presetDays[$data['type']];

        $data['gym_id']    = auth()->user()->gym_id;
        $data['is_active'] = $request->boolean('is_active', true);

        Plan::create($data);

        return redirect(gym_route('gym.plans.index'))->with('success', "Plan \"{$data['name']}\" created.");
    }

    public function show($gym ,Plan $plan)
    {
        abort_if($plan->gym_id !== auth()->user()->gym_id, 403);
        return redirect(gym_route('gym.plans.edit', [$gym, $plan]));
    }

    public function edit(Plan $plan)
    {
        abort_if($plan->gym_id !== auth()->user()->gym_id, 403);
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request,$gym, Plan $plan)
    {
        abort_if($plan->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'type'         => ['required', 'in:monthly,quarterly,half_yearly,yearly,custom'],
            'duration_days'=> ['required_if:type,custom', 'nullable', 'integer', 'min:1'],
            'price'        => ['required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $data['duration_days'] = $data['type'] === 'custom'
            ? $data['duration_days']
            : $this->presetDays[$data['type']];

        $data['is_active'] = $request->boolean('is_active');
        $plan->update($data);

        return redirect(gym_route('gym.plans.index'))->with('success', "Plan \"{$plan->name}\" updated.");
    }

    public function destroy(Plan $plan)
    {
        abort_if($plan->gym_id !== auth()->user()->gym_id, 403);

        if ($plan->memberPlans()->whereIn('status', ['active'])->exists()) {
            return back()->with('error', 'Cannot delete a plan with active members.');
        }

        $plan->delete();
        return redirect(gym_route('gym.plans.index'))->with('success', 'Plan deleted.');
    }
}
