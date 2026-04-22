<?php

namespace App\Http\Controllers;

use App\Models\WorkoutCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $gymId      = auth()->user()->gym_id;
        $categories = WorkoutCategory::where('gym_id', $gymId)
            ->withCount('activities')
            ->orderBy('name')
            ->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create() { return view('categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        WorkoutCategory::create($data);
        return redirect()->route('categories.index')->with('success', 'Category added.');
    }

    public function edit(WorkoutCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, WorkoutCategory $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);
        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(WorkoutCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
