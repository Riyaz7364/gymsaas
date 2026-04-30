<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSequence;
use App\Models\WorkoutSequenceDay;
use App\Models\WorkoutSequenceExercise;
use App\Models\WorkoutActivity;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkoutSequenceController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $showAiGenerator = auth()->user()->gymHasModule('ai_workout_plans');
        $membersForAi = $showAiGenerator
            ? Member::where('gym_id', $gym->id)->where('status', 'active')->orderBy('name')->get(['id', 'name', 'goal'])
            : collect();
        $sequences = WorkoutSequence::where('gym_id', $gym->id)
            ->with('days')
            ->latest()
            ->paginate(20);
        return view('workout-sequences.index', compact(['sequences', 'gym', 'showAiGenerator', 'membersForAi']));
    }

    public function create()
    {
        $gymId = auth()->user()->gym_id;
        $activities = WorkoutActivity::where('gym_id', $gymId)->orderBy('name')->get();
        return view('workout-sequences.create', compact('activities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_days' => 'required|integer|min:1|max:7',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'days' => 'required|array',
            'days.*.label' => 'required|string',
            'days.*.icon' => 'required|string',
            'days.*.color' => 'required|string',
            'days.*.bg' => 'required|string',
            'days.*.border' => 'required|string',
            'days.*.muscle_groups' => 'nullable|array',
            'days.*.exercises' => 'nullable|array',
            'days.*.exercises.*' => 'nullable|integer|exists:workout_activities,id',
        ]);

        $gymId = auth()->user()->gym_id;

        // Create sequence
        $sequence = WorkoutSequence::create([
            'gym_id' => $gymId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'total_days' => $data['total_days'],
            'is_default' => $request->boolean('is_default'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Create days and exercises
        foreach ($data['days'] as $dayNum => $dayData) {
            $day = WorkoutSequenceDay::create([
                'sequence_id' => $sequence->id,
                'day_number' => $dayNum + 1,
                'label' => $dayData['label'],
                'icon' => $dayData['icon'],
                'color' => $dayData['color'],
                'bg' => $dayData['bg'],
                'border' => $dayData['border'],
                'muscle_groups' => $dayData['muscle_groups'] ?? [],
            ]);

            // Add exercises (linked to activities)
            if (!empty($dayData['exercises'])) {
                foreach ($dayData['exercises'] as $index => $activityId) {
                    if ($activityId) {
                        WorkoutSequenceExercise::create([
                            'day_id' => $day->id,
                            'activity_id' => $activityId,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('workout-sequences.index')->with('success', 'Workout sequence created.');
    }

    public function edit($gym ,WorkoutSequence $workoutSequence)
    {
        $gym = auth()->user()->gym;
        if ($workoutSequence->gym_id !== $gym->id) {
            abort(403);
        }
        
        $activities = WorkoutActivity::where('gym_id', $gym->id)->orderBy('name')->get();
        return view('workout-sequences.create', compact('workoutSequence', 'activities', 'gym'));
    }

    public function update(Request $request, $gym, WorkoutSequence $workoutSequence)
    {
        $gymId = auth()->user()->gym_id;
        if ($workoutSequence->gym_id !== $gymId) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_days' => 'required|integer|min:1|max:7',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'days' => 'required|array',
            'days.*.label' => 'required|string',
            'days.*.icon' => 'required|string',
            'days.*.color' => 'required|string',
            'days.*.bg' => 'required|string',
            'days.*.border' => 'required|string',
            'days.*.muscle_groups' => 'nullable|array',
            'days.*.exercises' => 'nullable|array',
            'days.*.exercises.*' => 'nullable|integer|exists:workout_activities,id',
        ]);

        // Update sequence
        $workoutSequence->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'total_days' => $data['total_days'],
            'is_default' => $request->boolean('is_default'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Delete old days and exercises
        $workoutSequence->days()->each(function ($day) {
            $day->exercises()->delete();
            $day->delete();
        });

        // Create new days and exercises
        foreach ($data['days'] as $dayNum => $dayData) {
            $day = WorkoutSequenceDay::create([
                'sequence_id' => $workoutSequence->id,
                'day_number' => $dayNum + 1,
                'label' => $dayData['label'],
                'icon' => $dayData['icon'],
                'color' => $dayData['color'],
                'bg' => $dayData['bg'],
                'border' => $dayData['border'],
                'muscle_groups' => $dayData['muscle_groups'] ?? [],
            ]);

            // Add exercises (linked to activities)
            if (!empty($dayData['exercises'])) {
                foreach ($dayData['exercises'] as $index => $activityId) {
                    if ($activityId) {
                        WorkoutSequenceExercise::create([
                            'day_id' => $day->id,
                            'activity_id' => $activityId,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('workout-sequences.index')->with('success', 'Workout sequence updated.');
    }

    public function destroy(WorkoutSequence $workoutSequence)
    {
        $gymId = auth()->user()->gym_id;
        if ($workoutSequence->gym_id !== $gymId) {
            abort(403);
        }

        $workoutSequence->delete();
        return redirect()->route('workout-sequences.index')->with('success', 'Workout sequence deleted.');
    }

    public function generateAi(Request $request)
    {
        $gymId = auth()->user()->gym_id;
        $data = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        $member = Member::where('gym_id', $gymId)->findOrFail($data['member_id']);
        $goal = $member->goal ?? 'maintain';

        $focusMap = match ($goal) {
            'muscle_gain' => ['Chest', 'Back', 'Legs', 'Shoulders', 'Arms'],
            'weight_loss' => ['Full Body', 'Cardio', 'Legs', 'Core', 'Full Body'],
            'endurance' => ['Cardio', 'Legs', 'Full Body', 'Core', 'Cardio'],
            default => ['Upper Body', 'Lower Body', 'Core', 'Full Body', 'Cardio'],
        };

        $sequence = WorkoutSequence::create([
            'gym_id' => $gymId,
            'name' => 'AI Workout - ' . $member->name,
            'description' => 'AI generated workout sequence for ' . str_replace('_', ' ', $goal),
            'total_days' => count($focusMap),
            'is_default' => false,
            'is_active' => true,
        ]);

        $palette = [
            ['icon' => 'A', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'border' => '#93c5fd'],
            ['icon' => 'B', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            ['icon' => 'C', 'color' => '#10b981', 'bg' => '#f0fdf4', 'border' => '#6ee7b7'],
            ['icon' => 'D', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fcd34d'],
            ['icon' => 'E', 'color' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fca5a5'],
        ];

        $allActivities = WorkoutActivity::where('gym_id', $gymId)->get();

        foreach ($focusMap as $idx => $focus) {
            $style = $palette[$idx % count($palette)];
            $day = WorkoutSequenceDay::create([
                'sequence_id' => $sequence->id,
                'day_number' => $idx + 1,
                'label' => $focus . ' Day',
                'icon' => $style['icon'],
                'color' => $style['color'],
                'bg' => $style['bg'],
                'border' => $style['border'],
                'muscle_groups' => [$focus],
            ]);

            $focusLc = strtolower($focus);
            $activities = $allActivities->filter(function ($a) use ($focusLc) {
                return Str::contains(strtolower((string) $a->name), $focusLc)
                    || Str::contains(strtolower((string) $a->muscle_group), $focusLc)
                    || Str::contains(strtolower((string) $a->description), $focusLc);
            });

            if ($activities->isEmpty()) {
                $activities = $allActivities;
            }

            foreach ($activities->take(6)->values() as $aIndex => $activity) {
                WorkoutSequenceExercise::create([
                    'day_id' => $day->id,
                    'activity_id' => $activity->id,
                    'sort_order' => $aIndex + 1,
                ]);
            }
        }

        return redirect(gym_route('gym.workout-sequences.edit', [$sequence]))
            ->with('success', "AI workout sequence generated for {$member->name}.");
    }
}
