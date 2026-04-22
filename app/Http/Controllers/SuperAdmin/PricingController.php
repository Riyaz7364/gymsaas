<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        return view('super-admin.pricing.index', compact('plans'));
    }

    public function create()
    {
        $modules = Module::where('is_active', true)->orderBy('sort_order')->get()->groupBy('group_name');
        return view('super-admin.pricing.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:50|unique:subscription_plans,name',
            'display_name'  => 'required|string|max:100',
            'monthly_price' => 'required|numeric|min:0',
            'annual_price'  => 'required|numeric|min:0',
            'max_members'   => 'required|integer|min:-1',
            'max_trainers'  => 'required|integer|min:-1',
            'max_classes'   => 'required|integer|min:-1',
            'features'      => 'nullable|string',
            'modules'       => 'nullable|array',
            'modules.*'     => 'integer|exists:modules,id',
            'is_active'     => 'boolean',
            'sort_order'    => 'required|integer|min:0',
        ]);

        $data['features']  = $data['features']
            ? array_filter(array_map('trim', explode("\n", $data['features'])))
            : [];
        $data['is_active'] = $request->boolean('is_active');
        $moduleIds         = $request->input('modules', []);
        unset($data['modules']);

        $plan = SubscriptionPlan::create($data);
        $plan->modules()->sync($moduleIds);

        return redirect()->route('super-admin.pricing.index')->with('success', 'Plan created successfully.');
    }

    public function show(SubscriptionPlan $pricing)
    {
        $pricing->loadCount(['gymSubscriptions', 'activeSubscriptions']);
        return view('super-admin.pricing.show', compact('pricing'));
    }

    public function edit(SubscriptionPlan $pricing)
    {
        $modules = Module::where('is_active', true)->orderBy('sort_order')->get()->groupBy('group_name');
        $pricing->load('modules');
        return view('super-admin.pricing.edit', compact('pricing', 'modules'));
    }

    public function update(Request $request, SubscriptionPlan $pricing)
    {
        $data = $request->validate([
            'display_name'  => 'required|string|max:100',
            'monthly_price' => 'required|numeric|min:0',
            'annual_price'  => 'required|numeric|min:0',
            'max_members'   => 'required|integer|min:-1',
            'max_trainers'  => 'required|integer|min:-1',
            'max_classes'   => 'required|integer|min:-1',
            'features'      => 'nullable|string',
            'modules'       => 'nullable|array',
            'modules.*'     => 'integer|exists:modules,id',
            'is_active'     => 'boolean',
            'sort_order'    => 'required|integer|min:0',
        ]);

        $data['features']  = $data['features']
            ? array_filter(array_map('trim', explode("\n", $data['features'])))
            : [];
        $data['is_active'] = $request->boolean('is_active');
        $moduleIds         = $request->input('modules', []);
        unset($data['modules']);

        $pricing->update($data);
        $pricing->modules()->sync($moduleIds);

        return redirect()->route('super-admin.pricing.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $pricing)
    {
        if ($pricing->gymSubscriptions()->exists()) {
            return back()->with('error', 'Cannot delete a plan with active subscriptions.');
        }
        $pricing->delete();
        return redirect()->route('super-admin.pricing.index')->with('success', 'Plan deleted.');
    }
}