<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutCategory;
use Illuminate\Http\Request;

class WorkoutCategoryController extends Controller
{
    public function index()
    {
        $gymId      = auth()->user()->gym_id;
        $categories = WorkoutCategory::where('gym_id', $gymId)->withCount('activities')->orderBy('name')->paginate(20);
        return view('workout-categories.index', compact('categories'));
    }

    public function create() { return view('workout-categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        $data['gym_id'] = auth()->user()->gym_id;
        WorkoutCategory::create($data);
        return redirect()->route('workout-categories.index')->with('success', 'Category created.');
    }

    public function show(string $id) { return redirect()->route('workout-categories.index'); }

    public function edit(string $id)
    {
        $gymId    = auth()->user()->gym_id;
        $category = WorkoutCategory::where('gym_id', $gymId)->findOrFail($id);
        return view('workout-categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $gymId    = auth()->user()->gym_id;
        $category = WorkoutCategory::where('gym_id', $gymId)->findOrFail($id);
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        $category->update($data);
        return redirect()->route('workout-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(string $id)
    {
        $gymId = auth()->user()->gym_id;
        WorkoutCategory::where('gym_id', $gymId)->findOrFail($id)->delete();
        return redirect()->route('workout-categories.index')->with('success', 'Category deleted.');
    }
}