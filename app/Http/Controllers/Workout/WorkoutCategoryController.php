<?php

namespace App\Http\Controllers\Workout;

use App\Http\Controllers\Controller;
use App\Models\WorkoutCategory;
use Illuminate\Http\Request;

class WorkoutCategoryController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $categories = WorkoutCategory::where('gym_id', $gym->id)->withCount('activities')->orderBy('name')->paginate(20);
        return view('workout-categories.index', compact('categories', 'gym'));
    }

    public function create() { return view('workout-categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        $data['gym_id'] = auth()->user()->gym_id;
        WorkoutCategory::create($data);
        return redirect()->route('workout-categories.index')->with('success', 'Category created.');
    }

    public function show($gym,string $id) { return redirect()->route('workout-categories.index'); }

    public function edit($gym, string $id)
    {
        $category = WorkoutCategory::where('gym_id', $gym->id)->findOrFail($id);
        return view('workout-categories.edit', compact(['category', 'gym']));
    }

    public function update(Request $request, $gym, string $id)
    {
        $category = WorkoutCategory::where('gym_id', $gym->id)->findOrFail($id);
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        $category->update($data);
        return redirect()->route('workout-categories.index')->with('success', 'Category updated.');
    }

    public function destroy($gym, string $id)
    {
        $category = WorkoutCategory::where('gym_id', $gym->id)->findOrFail($id);
        $category->delete();
        return redirect()->route('workout-categories.index')->with('success', 'Category deleted.');
    }
}