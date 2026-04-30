<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutPlanController extends Controller
{
    public function index()
    {
        $trainer = Auth::guard('trainer')->user();
        $plans = WorkoutPlan::where('gym_id', $trainer->gym_id)
            ->where('trainer_id', $trainer->id)
            ->with('member')
            ->latest()
            ->paginate(20);

        return view('trainer.workout-plans.index', compact('plans'));
    }

    public function create()
    {
        $trainer = Auth::guard('trainer')->user();
        $members = $trainer->members()->orderBy('name')->get();
        return view('trainer.workout-plans.create', compact('members'));
    }

    public function store(Request $request)
    {
        $trainer = Auth::guard('trainer')->user();
        $memberIds = $trainer->members()->pluck('members.id')->toArray();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'member_id' => 'nullable|integer',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($data['member_id']) && !in_array($data['member_id'], $memberIds, true)) {
            abort(403);
        }

        $data['gym_id'] = $trainer->gym_id;
        $data['trainer_id'] = $trainer->id;
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');

        WorkoutPlan::create($data);

        return redirect()->route('trainer.workout-plans.index')->with('success', 'Workout plan created.');
    }

    public function edit($gym, WorkoutPlan $workoutPlan)
    {
        $trainer = Auth::guard('trainer')->user();
        abort_if($workoutPlan->gym_id !== $trainer->gym_id || $workoutPlan->trainer_id !== $trainer->id, 403);

        $members = $trainer->members()->orderBy('name')->get();
        return view('trainer.workout-plans.edit', compact('workoutPlan', 'members', 'gym'));
    }

    public function update(Request $request, WorkoutPlan $workoutPlan)
    {
        $trainer = Auth::guard('trainer')->user();
        abort_if($workoutPlan->gym_id !== $trainer->gym_id || $workoutPlan->trainer_id !== $trainer->id, 403);

        $memberIds = $trainer->members()->pluck('members.id')->toArray();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'member_id' => 'nullable|integer',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($data['member_id']) && !in_array($data['member_id'], $memberIds, true)) {
            abort(403);
        }

        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');

        $workoutPlan->update($data);

        return redirect()->route('trainer.workout-plans.index')->with('success', 'Workout plan updated.');
    }

    public function destroy(WorkoutPlan $workoutPlan)
    {
        $trainer = Auth::guard('trainer')->user();
        abort_if($workoutPlan->gym_id !== $trainer->gym_id || $workoutPlan->trainer_id !== $trainer->id, 403);

        $workoutPlan->delete();

        return redirect()->route('trainer.workout-plans.index')->with('success', 'Workout plan deleted.');
    }
}
