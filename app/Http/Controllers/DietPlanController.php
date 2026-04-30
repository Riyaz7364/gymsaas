<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Member;
use App\Models\FoodItem;
use App\Services\AiPlanGenerationService;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    public function index()
    {
        $gym = auth()->user()->gym;
        $query = DietPlan::where('gym_id', $gym->id)->with('member')->latest();
        $showAiPlans = auth()->user()->gymHasModule('ai_diet_plans');
        $membersForAi = $showAiPlans
            ? Member::where('gym_id', $gym->id)->where('status', 'active')->orderBy('name')->get(['id', 'name', 'goal'])
            : collect();

        $type = request('type', 'ai');

        if ($type === 'default') {
            $query->where('is_default', true)->where('ai_generated', false);
        } elseif ($type === 'ai') {
            $query->where('ai_generated', true);
        } elseif ($type === 'custom') {
            $query->where('is_default', false)->where('ai_generated', false);
        }

        $plans = $query->paginate(20);
        return view('diet-plans.index', compact('plans', 'showAiPlans', 'membersForAi','gym'));
    }

    public function create()
    {
        $gym = auth()->user()->gym;
        $members = Member::where('gym_id', $gym->id)->orderBy('name')->get();
        $foodItems = FoodItem::where('gym_id', $gym->id)->with('category')->orderBy('name')->get();
        return view('diet-plans.create', compact('members', 'foodItems', 'gym'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'goal'        => 'required|in:weight_loss,muscle_gain,maintain,endurance',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'is_default'  => 'nullable|boolean',
            'meals'       => 'nullable|array',
            'meals.*.name' => 'required_with:meals|string|max:150',
            'meals.*.time' => 'nullable|string|max:20',
            'meals.*.food_items' => 'nullable|array',
            'meals.*.food_items.*.food_item_id' => 'required|exists:food_items,id',
            'meals.*.food_items.*.quantity' => 'required|numeric|min:0.1',
            'meals.*.total_calories' => 'nullable|integer|min:0',
            'meals.*.protein_g' => 'nullable|numeric|min:0',
            'meals.*.carbs_g' => 'nullable|numeric|min:0',
            'meals.*.fat_g' => 'nullable|numeric|min:0',
            'meals.*.calculated_calories' => 'nullable|integer|min:0',
            'meals.*.calculated_protein' => 'nullable|numeric|min:0',
            'meals.*.calculated_carbs' => 'nullable|numeric|min:0',
            'meals.*.calculated_fat' => 'nullable|numeric|min:0',
        ]);

        $data['gym_id']    = auth()->user()->gym_id;
        $data['created_by'] = auth()->id();
        $data['is_active'] = true;
        $data['is_default'] = $request->boolean('is_default');

        $dietPlan = DietPlan::create($data);

        // Create meals if provided
        if ($request->has('meals') && is_array($request->meals)) {
            foreach ($request->meals as $mealData) {
                if (!empty($mealData['name'])) {
                    // Prepare food items data
                    $foodItems = [];
                    if (isset($mealData['food_items']) && is_array($mealData['food_items'])) {
                        foreach ($mealData['food_items'] as $foodItemData) {
                            if (!empty($foodItemData['food_item_id'])) {
                                $foodItem = FoodItem::find($foodItemData['food_item_id']);
                                if ($foodItem) {
                                    $quantity = $foodItemData['quantity'] ?? 1;
                                    $foodItems[] = [
                                        'food_item_id' => $foodItem->id,
                                        'name' => $foodItem->name,
                                        'quantity' => $quantity,
                                        'serving_size' => $foodItem->serving_size,
                                        'serving_unit' => $foodItem->serving_unit,
                                        'calories' => $foodItem->calories * $quantity,
                                        'protein_g' => $foodItem->protein_g * $quantity,
                                        'carbs_g' => $foodItem->carbs_g * $quantity,
                                        'fat_g' => $foodItem->fat_g * $quantity,
                                    ];
                                }
                            }
                        }
                    }

                    // Calculate totals from food items or use manual entry
                    $totalCalories = 0;
                    $totalProtein = 0;
                    $totalCarbs = 0;
                    $totalFat = 0;

                    if (!empty($foodItems)) {
                        // Use calculated values from food items
                        foreach ($foodItems as $item) {
                            $totalCalories += $item['calories'];
                            $totalProtein += $item['protein_g'];
                            $totalCarbs += $item['carbs_g'];
                            $totalFat += $item['fat_g'];
                        }
                    } else {
                        // Use manual entry values
                        $totalCalories = $mealData['total_calories'] ?? $mealData['calculated_calories'] ?? 0;
                        $totalProtein = $mealData['protein_g'] ?? $mealData['calculated_protein'] ?? 0;
                        $totalCarbs = $mealData['carbs_g'] ?? $mealData['calculated_carbs'] ?? 0;
                        $totalFat = $mealData['fat_g'] ?? $mealData['calculated_fat'] ?? 0;
                    }

                    $dietPlan->meals()->create([
                        'name' => $mealData['name'],
                        'time' => $mealData['time'] ?? null,
                        'total_calories' => round($totalCalories),
                        'protein_g' => round($totalProtein, 2),
                        'carbs_g' => round($totalCarbs, 2),
                        'fat_g' => round($totalFat, 2),
                        'meal_type' => 'breakfast', // default
                        'day_of_week' => 'all',
                        'foods' => $foodItems,
                        'sort_order' => $dietPlan->meals()->max('sort_order') + 1,
                    ]);
                }
            }
        }

        return redirect(gym_route('gym.diet-plans.index'))->with('success', 'Diet plan created successfully.');
    }

    public function edit($gym,DietPlan $dietPlan)
    {
        $gym   = auth()->user()->gym;
        $members = Member::where('gym_id', $gym->id)->orderBy('name')->get();
        $foodItems = FoodItem::where('gym_id', $gym->id)->with('category')->orderBy('name')->get();
        return view('diet-plans.edit', compact('dietPlan', 'members', 'foodItems', 'gym'));
    }

    public function update(Request $request,$gym, DietPlan $dietPlan)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'goal'        => 'required|in:weight_loss,muscle_gain,maintain,endurance',
            'description' => 'nullable|string',
            'member_id'   => 'nullable|exists:members,id',
            'is_default'  => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
            'meals'       => 'nullable|array',
            'meals.*.id' => 'nullable|exists:diet_meals,id',
            'meals.*.name' => 'required_with:meals|string|max:150',
            'meals.*.time' => 'nullable|string|max:20',
            'meals.*.total_calories' => 'nullable|integer|min:0',
            'meals.*.protein_g' => 'nullable|numeric|min:0',
            'meals.*.carbs_g' => 'nullable|numeric|min:0',
            'meals.*.fat_g' => 'nullable|numeric|min:0',
            'meals.*.foods_text' => 'nullable|string',
        ]);

        $data['is_default'] = $request->boolean('is_default');
        $data['is_active']  = $request->boolean('is_active');
        $dietPlan->update($data);

        // Handle meals updates
        if ($request->has('meals') && is_array($request->meals)) {
            $existingMealIds = $dietPlan->meals->pluck('id')->toArray();
            $updatedMealIds = [];

            foreach ($request->meals as $index => $mealData) {
                if (!empty($mealData['name'])) {
                    // Check if this is an existing meal (has an ID in the form)
                    $mealId = $mealData['id'] ?? null;

                    // Prepare food items data
                    $foodItems = [];
                    if (isset($mealData['food_items']) && is_array($mealData['food_items'])) {
                        foreach ($mealData['food_items'] as $foodItemData) {
                            if (!empty($foodItemData['food_item_id'])) {
                                $foodItem = FoodItem::find($foodItemData['food_item_id']);
                                if ($foodItem) {
                                    $quantity = $foodItemData['quantity'] ?? 1;
                                    $foodItems[] = [
                                        'food_item_id' => $foodItem->id,
                                        'name' => $foodItem->name,
                                        'quantity' => $quantity,
                                        'serving_size' => $foodItem->serving_size,
                                        'serving_unit' => $foodItem->serving_unit,
                                        'calories' => $foodItem->calories * $quantity,
                                        'protein_g' => $foodItem->protein_g * $quantity,
                                        'carbs_g' => $foodItem->carbs_g * $quantity,
                                        'fat_g' => $foodItem->fat_g * $quantity,
                                    ];
                                }
                            }
                        }
                    }

                    // Calculate totals from food items or use manual entry
                    $totalCalories = 0;
                    $totalProtein = 0;
                    $totalCarbs = 0;
                    $totalFat = 0;

                    if (!empty($foodItems)) {
                        // Use calculated values from food items
                        foreach ($foodItems as $item) {
                            $totalCalories += $item['calories'];
                            $totalProtein += $item['protein_g'];
                            $totalCarbs += $item['carbs_g'];
                            $totalFat += $item['fat_g'];
                        }
                    } else {
                        // Use manual entry values
                        $totalCalories = $mealData['total_calories'] ?? $mealData['calculated_calories'] ?? 0;
                        $totalProtein = $mealData['protein_g'] ?? $mealData['calculated_protein'] ?? 0;
                        $totalCarbs = $mealData['carbs_g'] ?? $mealData['calculated_carbs'] ?? 0;
                        $totalFat = $mealData['fat_g'] ?? $mealData['calculated_fat'] ?? 0;
                    }

                    if ($mealId && in_array($mealId, $existingMealIds)) {
                        // Update existing meal
                        $meal = $dietPlan->meals()->find($mealId);
                        if ($meal) {
                            $meal->update([
                                'name' => $mealData['name'],
                                'time' => $mealData['time'] ?? null,
                                'total_calories' => round($totalCalories),
                                'protein_g' => round($totalProtein, 2),
                                'carbs_g' => round($totalCarbs, 2),
                                'fat_g' => round($totalFat, 2),
                                'foods' => $foodItems,
                            ]);
                            $updatedMealIds[] = $mealId;
                        }
                    } else {
                        // Create new meal
                        $dietPlan->meals()->create([
                            'name' => $mealData['name'],
                            'time' => $mealData['time'] ?? null,
                            'total_calories' => round($totalCalories),
                            'protein_g' => round($totalProtein, 2),
                            'carbs_g' => round($totalCarbs, 2),
                            'fat_g' => round($totalFat, 2),
                            'meal_type' => 'breakfast',
                            'day_of_week' => 'all',
                            'foods' => $foodItems,
                            'sort_order' => $dietPlan->meals()->max('sort_order') + 1,
                        ]);
                    }
                }
            }

            // Remove meals that are no longer in the form
            $mealsToDelete = array_diff($existingMealIds, $updatedMealIds);
            if (!empty($mealsToDelete)) {
                $dietPlan->meals()->whereIn('id', $mealsToDelete)->delete();
            }
        } else {
            // If no meals in request, remove all existing meals
            $dietPlan->meals()->delete();
        }

        return redirect(gym_route('gym.diet-plans.index'))->with('success', 'Diet plan updated.');
    }

    public function destroy(DietPlan $dietPlan)
    {
        $dietPlan->delete();
        return redirect(gym_route('gym.diet-plans.index'))->with('success', 'Diet plan deleted.');
    }

    public function mealStore(Request $request, DietPlan $dietPlan)
    {
        abort_if($dietPlan->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'time'           => 'nullable|string|max:20',
            'total_calories' => 'nullable|integer|min:0',
            'protein_g'      => 'nullable|numeric|min:0',
            'carbs_g'        => 'nullable|numeric|min:0',
            'fat_g'          => 'nullable|numeric|min:0',
        ]);

        $data['plan_id']    = $dietPlan->id;
        $data['meal_type']  = 'breakfast'; // default, not used in simple view
        $data['day_of_week'] = 'all';
        $data['foods']      = [];
        $data['sort_order'] = $dietPlan->meals()->max('sort_order') + 1;

        $dietPlan->meals()->create($data);

        return back()->with('success', 'Meal added.');
    }

    public function mealDestroy(DietPlan $dietPlan, \App\Models\DietMeal $meal)
    {
        abort_if($dietPlan->gym_id !== auth()->user()->gym_id, 403);
        abort_if($meal->plan_id !== $dietPlan->id, 403);

        $meal->delete();

        return back()->with('success', 'Meal removed.');
    }

    public function generateAi(Request $request, AiPlanGenerationService $aiPlanGeneration)
    {
        $gymId = auth()->user()->gym_id;
        $data = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        $member = Member::where('gym_id', $gymId)->findOrFail($data['member_id']);
        $plan = $aiPlanGeneration->generateDietPlan($member, auth()->id());

        return redirect(gym_route('gym.diet-plans.edit', [$plan]))
            ->with('success', "AI diet plan generated for {$member->name}.");
    }
}

