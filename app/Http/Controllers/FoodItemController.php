<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index()
    {
        $gymId       = auth()->user()->gym_id;
        $categories  = FoodCategory::where('gym_id', $gymId)->orderBy('name')->get();
        $query       = FoodItem::where('gym_id', $gymId)->with('category');

        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        $foodItems = $query->latest()->paginate(20);

        return view('food-management.items.index', compact('foodItems', 'categories'));
    }

    public function create()
    {
        $gymId      = auth()->user()->gym_id;
        $categories = FoodCategory::where('gym_id', $gymId)->orderBy('name')->get();
        return view('food-management.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:food_categories,id',
            'name'        => 'required|string|max:150',
            'serving_size' => 'nullable|string|max:50',
            'serving_unit' => 'required|string|max:20|in:g,ml,oz,cup,tbsp,tsp,piece',
            'calories'    => 'nullable|integer|min:0',
            'protein_g'   => 'nullable|numeric|min:0',
            'carbs_g'     => 'nullable|numeric|min:0',
            'fat_g'       => 'nullable|numeric|min:0',
            'fiber_g'     => 'nullable|numeric|min:0',
        ]);

        $data['gym_id'] = auth()->user()->gym_id;

        // Verify category belongs to gym
        $category = FoodCategory::find($data['category_id']);
        abort_if($category->gym_id !== auth()->user()->gym_id, 403);

        FoodItem::create($data);

        return redirect(gym_route('gym.food-items.index'))->with('success', 'Food item created successfully.');
    }

    public function edit(FoodItem $foodItem)
    {
        abort_if($foodItem->gym_id !== auth()->user()->gym_id, 403);
        $gymId      = auth()->user()->gym_id;
        $categories = FoodCategory::where('gym_id', $gymId)->orderBy('name')->get();
        return view('food-management.items.edit', compact('foodItem', 'categories'));
    }

    public function update(Request $request, FoodItem $foodItem)
    {
        abort_if($foodItem->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'category_id' => 'required|exists:food_categories,id',
            'name'        => 'required|string|max:150',
            'serving_size' => 'nullable|string|max:50',
            'serving_unit' => 'required|string|max:20|in:g,ml,oz,cup,tbsp,tsp,piece',
            'calories'    => 'nullable|integer|min:0',
            'protein_g'   => 'nullable|numeric|min:0',
            'carbs_g'     => 'nullable|numeric|min:0',
            'fat_g'       => 'nullable|numeric|min:0',
            'fiber_g'     => 'nullable|numeric|min:0',
        ]);

        // Verify category belongs to gym
        $category = FoodCategory::find($data['category_id']);
        abort_if($category->gym_id !== auth()->user()->gym_id, 403);

        $foodItem->update($data);

        return redirect(gym_route('gym.food-items.index'))->with('success', 'Food item updated successfully.');
    }

    public function destroy(FoodItem $foodItem)
    {
        abort_if($foodItem->gym_id !== auth()->user()->gym_id, 403);
        $foodItem->delete();
        return redirect(gym_route('gym.food-items.index'))->with('success', 'Food item deleted successfully.');
    }
}
