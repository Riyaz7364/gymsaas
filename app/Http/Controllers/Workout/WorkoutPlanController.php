<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use App\Models\Member;
use App\Models\Trainer;
use Illuminate\Http\Request;

class WorkoutPlanController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $plans = WorkoutPlan::where('gym_id', $gymId)
            ->with(['member', 'trainer'])
            ->latest()
            ->paginate(20);
        return view('workout-plans.index', compact('plans'));
    }

    public function create()
    {
        $gymId    = auth()->user()->gym_id;
        $members  = Member::where('gym_id', $gymId)->orderBy('name')->get();
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('workout-plans.create', compact('members', 'trainers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'trainer_id'  => 'nullable|exists:trainers,id',
            'is_default'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['gym_id']     = auth()->user()->gym_id;
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active']  = $request->boolean('is_active');
        WorkoutPlan::create($data);
        return redirect()->route('workout-plans.index')->with('success', 'Workout plan created.');
    }

    public function edit(WorkoutPlan $workoutPlan)
    {
        $gymId    = auth()->user()->gym_id;
        $members  = Member::where('gym_id', $gymId)->orderBy('name')->get();
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('workout-plans.edit', ['plan' => $workoutPlan, 'members' => $members, 'trainers' => $trainers]);
    }

    public function update(Request $request, WorkoutPlan $workoutPlan)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'trainer_id'  => 'nullable|exists:trainers,id',
            'is_default'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active']  = $request->boolean('is_active');
        $workoutPlan->update($data);
        return redirect()->route('workout-plans.index')->with('success', 'Workout plan updated.');
    }

    public function destroy(WorkoutPlan $workoutPlan)
    {
        $workoutPlan->delete();
        return redirect()->route('workout-plans.index')->with('success', 'Workout plan deleted.');
    }
}