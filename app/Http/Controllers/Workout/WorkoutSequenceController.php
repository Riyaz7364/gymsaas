<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSequence;
use App\Models\WorkoutSequenceDay;
use App\Models\WorkoutSequenceExercise;
use App\Models\WorkoutActivity;
use Illuminate\Http\Request;

class WorkoutSequenceController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $sequences = WorkoutSequence::where('gym_id', $gymId)
            ->with('days')
            ->latest()
            ->paginate(20);
        return view('workout-sequences.index', compact('sequences'));
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

    public function edit(WorkoutSequence $workoutSequence)
    {
        $gymId = auth()->user()->gym_id;
        if ($workoutSequence->gym_id !== $gymId) {
            abort(403);
        }
        
        $activities = WorkoutActivity::where('gym_id', $gymId)->orderBy('name')->get();
        return view('workout-sequences.create', compact('workoutSequence', 'activities'));
    }

    public function update(Request $request, WorkoutSequence $workoutSequence)
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
}
