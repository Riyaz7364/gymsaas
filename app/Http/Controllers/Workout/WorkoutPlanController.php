<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use App\Models\Member;
use App\Models\Trainer;
use App\Services\AiPlanGenerationService;
use Illuminate\Http\Request;

class WorkoutPlanController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $showAiGenerator = auth()->user()->gymHasModule('ai_workout_plans');
        $membersForAi = $showAiGenerator
            ? Member::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get(['id', 'name', 'goal'])
            : collect();

        $plans = WorkoutPlan::where('gym_id', $gymId)
            ->with(['member', 'trainer'])
            ->latest()
            ->paginate(20);
        return view('workout-plans.index', compact('plans', 'showAiGenerator', 'membersForAi'));
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
        return redirect(gym_route('gym.workout-plans.index'))->with('success', 'Workout plan created.');
    }

    public function edit($gym, WorkoutPlan $workoutPlan)
    {
        $gymId    = $gym->id;
        $members  = Member::where('gym_id', $gymId)->orderBy('name')->get();
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        return view('workout-plans.edit', ['plan' => $workoutPlan, 'members' => $members, 'trainers' => $trainers, 'gym' => $gym]);
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
        return redirect(gym_route('gym.workout-plans.index'))->with('success', 'Workout plan updated.');
    }

    public function destroy(WorkoutPlan $workoutPlan)
    {
        $workoutPlan->delete();
        return redirect(gym_route('gym.workout-plans.index'))->with('success', 'Workout plan deleted.');
    }

    public function generateAi(Request $request, AiPlanGenerationService $aiPlanGeneration)
    {
        $gymId = auth()->user()->gym_id;
        $data = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        $member = Member::where('gym_id', $gymId)->findOrFail($data['member_id']);
        $plan = $aiPlanGeneration->generateWorkoutPlan($member);

        return redirect(gym_route('gym.workout-plans.edit', [$plan]))
            ->with('success', "AI workout plan generated for {$member->name}.");
    }
}
