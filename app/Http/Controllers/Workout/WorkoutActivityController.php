<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutActivity;
use App\Models\WorkoutCategory;
use Illuminate\Http\Request;

class WorkoutActivityController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $activities = WorkoutActivity::where('gym_id', $gym->id)
            ->with('category')
            ->orderBy('name')
            ->paginate(20);
        return view('workout-activities.index', compact(['activities', 'gym']));
    }

    public function create()
    {
        $gym = auth()->user()->gym;
        $gymId = $gym->id;
        $categories = WorkoutCategory::where('gym_id', $gymId)->orderBy('name')->get();
        return view('workout-activities.create', compact(['categories', 'gym']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'category_id'  => 'nullable|exists:workout_categories,id',
            'muscle_group' => 'nullable|string|max:100',
            'equipment'    => 'nullable|string|max:100',
            'difficulty'   => 'nullable|in:beginner,intermediate,advanced',
            'video_url'    => 'nullable|url',
            'description'  => 'nullable|string',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        WorkoutActivity::create($data);
        return redirect()->route('workout-activities.index')->with('success', 'Exercise added.');
    }

    public function edit($gym,WorkoutActivity $workoutActivity)
    {
        $gym = auth()->user()->gym;
        $gymId = $gym->id;
        $categories = WorkoutCategory::where('gym_id', $gymId)->orderBy('name')->get();
        return view('workout-activities.edit', ['activity' => $workoutActivity, 'categories' => $categories, 'gym' => $gym]);
    }

    public function update(Request $request,$gym, WorkoutActivity $workoutActivity)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'category_id'  => 'nullable|exists:workout_categories,id',
            'muscle_group' => 'nullable|string|max:100',
            'equipment'    => 'nullable|string|max:100',
            'difficulty'   => 'nullable|in:beginner,intermediate,advanced',
            'video_url'    => 'nullable|url',
            'description'  => 'nullable|string',
        ]);
        $workoutActivity->update($data);
        return redirect()->route('workout-activities.index')->with('success', 'Exercise updated.');
    }

    public function destroy(WorkoutActivity $workoutActivity)
    {
        $workoutActivity->delete();
        return redirect()->route('workout-activities.index')->with('success', 'Exercise deleted.');
    }
}