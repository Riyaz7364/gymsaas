<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $categories = FoodCategory::where('gym_id', $gym->id)->withCount('foodItems')->latest()->paginate(20);
        return view('food-management.categories.index', compact('categories', 'gym'));
    }

    public function create()
    {
        return view('food-management.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:food_categories',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:20',
        ]);

        $data['gym_id'] = auth()->user()->gym_id;
        FoodCategory::create($data);

        return redirect(gym_route('gym.food-categories.index'))->with('success', 'Category created successfully.');
    }

    public function edit($gym, FoodCategory $foodCategory)
    {
        abort_if($foodCategory->gym_id !== auth()->user()->gym_id, 403);
        return view('food-management.categories.edit', compact('foodCategory', 'gym'));
    }

    public function update(Request $request, $gym, FoodCategory $foodCategory)
    {
        abort_if($foodCategory->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:food_categories,name,' . $foodCategory->id,
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:20',
        ]);

        $foodCategory->update($data);

        return redirect(gym_route('gym.food-categories.index'))->with('success', 'Category updated successfully.');
    }

    public function destroy($gym, FoodCategory $foodCategory)
    {
        abort_if($foodCategory->gym_id !== auth()->user()->gym_id, 403);
        $foodCategory->delete();
        return redirect(gym_route('gym.food-categories.index'))->with('success', 'Category deleted successfully.');
    }
}
