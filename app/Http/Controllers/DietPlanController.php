<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Member;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $query = DietPlan::where('gym_id', $gymId)->with('member')->latest();
        if (request('type') === 'default') {
            $query->where('is_default', true);
        } elseif (request('type') === 'custom') {
            $query->where('is_default', false);
        }
        $plans = $query->paginate(20);
        return view('diet-plans.index', compact('plans'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('diet-plans.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'goal'        => 'nullable|in:weight_loss,muscle_gain,maintain,endurance',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'is_default'  => 'nullable|boolean',
        ]);
        $data['gym_id']    = auth()->user()->gym_id;
        $data['created_by'] = auth()->id();
        $data['is_active'] = true;
        $data['is_default'] = $request->boolean('is_default');
        DietPlan::create($data);
        return redirect()->route('diet-plans.index')->with('success', 'Diet plan created.');
    }

    public function edit(DietPlan $dietPlan)
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('diet-plans.edit', compact('dietPlan', 'members'));
    }

    public function update(Request $request, DietPlan $dietPlan)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'goal'        => 'nullable|in:weight_loss,muscle_gain,maintain,endurance',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'is_default'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active']  = $request->boolean('is_active');
        $dietPlan->update($data);
        return redirect()->route('diet-plans.index')->with('success', 'Diet plan updated.');
    }

    public function destroy(DietPlan $dietPlan)
    {
        $dietPlan->delete();
        return redirect()->route('diet-plans.index')->with('success', 'Diet plan deleted.');
    }

    public function mealStore(Request $request, DietPlan $dietPlan)
    {
        abort_if($dietPlan->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'time'           => 'nullable|string|max:20',
            'total_calories' => 'nullable|integer|min:0',
            'protein_g'      => 'nullable|numeric|min:0',
            'carbs_g'        => 'nullable|numeric|min:0',
            'fat_g'          => 'nullable|numeric|min:0',
        ]);

        $data['plan_id']    = $dietPlan->id;
        $data['meal_type']  = 'breakfast'; // default, not used in simple view
        $data['day_of_week'] = 'all';
        $data['foods']      = [];
        $data['sort_order'] = $dietPlan->meals()->max('sort_order') + 1;

        $dietPlan->meals()->create($data);

        return back()->with('success', 'Meal added.');
    }

    public function mealDestroy(DietPlan $dietPlan, \App\Models\DietMeal $meal)
    {
        abort_if($dietPlan->gym_id !== auth()->user()->gym_id, 403);
        abort_if($meal->plan_id !== $dietPlan->id, 403);

        $meal->delete();

        return back()->with('success', 'Meal removed.');
    }

    public function generateAi(DietPlan $dietPlan)
    {
        // AI generation stub — implement when AI service is configured
        return redirect()->route('diet-plans.edit', $dietPlan)
            ->with('info', 'AI generation coming soon.');
    }
}