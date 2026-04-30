<?php

namespace App\Services;

use App\Models\DietPlan;
use App\Models\FoodItem;
use App\Models\Member;
use App\Models\WorkoutActivity;
use App\Models\WorkoutPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPlanGenerationService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.key', '');
        $this->model = (string) config('services.openai.model', 'gpt-4o');
    }

    public function generateDietPlan(Member $member, int $createdBy): DietPlan
    {
        $gymId = $member->gym_id;
        $goal = $member->goal ?? 'maintain';
        $foodItems = FoodItem::where('gym_id', $gymId)->limit(120)->get();

        $dailyCalories = match ($goal) {
            'weight_loss' => 1800,
            'muscle_gain' => 2600,
            'endurance' => 2400,
            default => 2200,
        };

        $mealBlueprint = $this->buildDietBlueprint($goal, $dailyCalories, $foodItems);
        $aiOverride = $this->generateDietBlueprintFromAi($member, $dailyCalories);
        if (is_array($aiOverride) && !empty($aiOverride['meals'])) {
            $mealBlueprint = $aiOverride['meals'];
        }

        $plan = DietPlan::create([
            'gym_id' => $gymId,
            'member_id' => $member->id,
            'created_by' => $createdBy,
            'name' => "AI Diet - {$member->name}",
            'description' => 'AI generated diet plan based on goal, attendance and workout context.',
            'goal' => $goal,
            'is_default' => false,
            'ai_generated' => true,
            'ai_prompt_used' => 'member_goal_and_activity',
            'is_active' => true,
        ]);

        foreach ($mealBlueprint as $i => $meal) {
            $foods = $this->resolveMealFoods($foodItems, $meal['focus'] ?? 'balanced');
            $totals = $this->sumMealNutrition($foods);

            $plan->meals()->create([
                'name' => $meal['name'] ?? ('Meal ' . ($i + 1)),
                'time' => $meal['time'] ?? null,
                'meal_type' => $meal['meal_type'] ?? 'breakfast',
                'day_of_week' => 'all',
                'foods' => $foods,
                'total_calories' => (int) round($totals['calories']),
                'protein_g' => round($totals['protein_g'], 2),
                'carbs_g' => round($totals['carbs_g'], 2),
                'fat_g' => round($totals['fat_g'], 2),
                'sort_order' => $i + 1,
            ]);
        }

        return $plan;
    }

    public function generateWorkoutPlan(Member $member): WorkoutPlan
    {
        $gymId = $member->gym_id;
        $goal = $member->goal ?? 'maintain';
        $activities = WorkoutActivity::where('gym_id', $gymId)->get();

        $plan = WorkoutPlan::create([
            'gym_id' => $gymId,
            'member_id' => $member->id,
            'trainer_id' => $member->trainer->first()?->id,
            'name' => "AI Workout - {$member->name}",
            'description' => 'AI generated workout plan based on goal and available activities.',
            'is_default' => false,
            'is_active' => true,
        ]);

        $dayMap = ['mon', 'tue', 'wed', 'thu', 'fri'];
        $focusByDay = $this->workoutFocusByGoal($goal);
        $aiFocus = $this->generateWorkoutFocusFromAi($member);
        if (is_array($aiFocus) && count($aiFocus) >= 3) {
            $focusByDay = array_values($aiFocus);
        }

        foreach ($dayMap as $dayIndex => $dayCode) {
            $focus = $focusByDay[$dayIndex] ?? 'full body';
            $selected = $this->pickActivitiesForFocus($activities, $focus, 6);

            foreach ($selected as $sort => $activity) {
                $plan->items()->create([
                    'activity_id' => $activity->id,
                    'day_of_week' => $dayCode,
                    'sets' => $goal === 'endurance' ? 3 : 4,
                    'reps' => $goal === 'muscle_gain' ? '8-12' : '12-15',
                    'duration_secs' => $goal === 'endurance' ? 120 : null,
                    'rest_secs' => $goal === 'endurance' ? 45 : 75,
                    'sort_order' => $sort + 1,
                    'notes' => 'AI suggested',
                ]);
            }
        }

        return $plan;
    }

    private function buildDietBlueprint(string $goal, int $dailyCalories, Collection $foodItems): array
    {
        $mealTypes = [
            ['name' => 'Breakfast', 'time' => '08:00 AM', 'meal_type' => 'breakfast', 'focus' => 'balanced'],
            ['name' => 'Mid-Morning Snack', 'time' => '11:00 AM', 'meal_type' => 'morning_snack', 'focus' => 'protein'],
            ['name' => 'Lunch', 'time' => '01:30 PM', 'meal_type' => 'lunch', 'focus' => 'balanced'],
            ['name' => 'Evening Snack', 'time' => '05:00 PM', 'meal_type' => 'evening_snack', 'focus' => 'carbs'],
            ['name' => 'Dinner', 'time' => '08:30 PM', 'meal_type' => 'dinner', 'focus' => $goal === 'weight_loss' ? 'protein' : 'balanced'],
        ];

        if ($foodItems->isEmpty()) {
            return $mealTypes;
        }

        return $mealTypes;
    }

    private function resolveMealFoods(Collection $foods, string $focus): array
    {
        if ($foods->isEmpty()) {
            return [];
        }

        $pool = match ($focus) {
            'protein' => $foods->sortByDesc('protein_g'),
            'carbs' => $foods->sortByDesc('carbs_g'),
            default => $foods->sortByDesc(fn ($f) => ($f->protein_g + $f->carbs_g + $f->fat_g)),
        };

        return $pool->take(3)->map(function (FoodItem $item) {
            return [
                'food_item_id' => $item->id,
                'name' => $item->name,
                'quantity' => 1,
                'serving_size' => $item->serving_size,
                'serving_unit' => $item->serving_unit,
                'calories' => (float) $item->calories,
                'protein_g' => (float) $item->protein_g,
                'carbs_g' => (float) $item->carbs_g,
                'fat_g' => (float) $item->fat_g,
            ];
        })->values()->all();
    }

    private function sumMealNutrition(array $foods): array
    {
        return collect($foods)->reduce(function (array $carry, array $food) {
            $carry['calories'] += (float) ($food['calories'] ?? 0);
            $carry['protein_g'] += (float) ($food['protein_g'] ?? 0);
            $carry['carbs_g'] += (float) ($food['carbs_g'] ?? 0);
            $carry['fat_g'] += (float) ($food['fat_g'] ?? 0);
            return $carry;
        }, ['calories' => 0, 'protein_g' => 0, 'carbs_g' => 0, 'fat_g' => 0]);
    }

    private function workoutFocusByGoal(string $goal): array
    {
        return match ($goal) {
            'muscle_gain' => ['chest', 'back', 'legs', 'shoulders', 'arms'],
            'weight_loss' => ['full body', 'cardio', 'legs', 'core', 'full body'],
            'endurance' => ['cardio', 'legs', 'full body', 'core', 'cardio'],
            default => ['full body', 'upper body', 'legs', 'core', 'full body'],
        };
    }

    private function pickActivitiesForFocus(Collection $activities, string $focus, int $limit): Collection
    {
        $focusLc = strtolower($focus);
        $matched = $activities->filter(function ($a) use ($focusLc) {
            return str_contains(strtolower((string) $a->name), $focusLc)
                || str_contains(strtolower((string) $a->muscle_group), $focusLc)
                || str_contains(strtolower((string) $a->description), $focusLc);
        });

        if ($matched->count() < 3) {
            $matched = $activities;
        }

        return $matched->take($limit);
    }

    private function generateDietBlueprintFromAi(Member $member, int $dailyCalories): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $prompt = "Create a 5-meal gym diet blueprint in JSON only. Member goal: {$member->goal}. Daily calories target: {$dailyCalories}. ".
            'Output format: {"meals":[{"name":"Breakfast","time":"08:00 AM","meal_type":"breakfast","focus":"protein"}]}';

        return $this->requestJson($prompt);
    }

    private function generateWorkoutFocusFromAi(Member $member): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $prompt = "Suggest exactly 5 workout day focus labels in JSON for gym member goal {$member->goal}. ".
            'Output format: {"days":["chest","back","legs","shoulders","arms"]}';

        $json = $this->requestJson($prompt);
        return is_array($json['days'] ?? null) ? $json['days'] : null;
    }

    private function requestJson(string $prompt): ?array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(25)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'temperature' => 0.3,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Return valid minified JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                return null;
            }

            $content = (string) $response->json('choices.0.message.content', '');
            $parsed = json_decode($content, true);
            if (is_array($parsed)) {
                return $parsed;
            }

            if (preg_match('/\{.*\}/s', $content, $m)) {
                $parsed2 = json_decode($m[0], true);
                return is_array($parsed2) ? $parsed2 : null;
            }
        } catch (\Throwable $e) {
            Log::warning('AI plan generation fallback: ' . $e->getMessage());
        }

        return null;
    }
}

