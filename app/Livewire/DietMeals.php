<?php

namespace App\Livewire;

use App\Models\DietMeal;
use App\Models\DietPlan;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DietMeals extends Component
{
    public DietPlan $dietPlan;

    #[Validate('required|string|max:150')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public ?string $time = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $total_calories = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $protein_g = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $carbs_g = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $fat_g = null;

    public function addMeal(): void
    {
        abort_if($this->dietPlan->gym_id !== auth()->user()->gym_id, 403);

        $this->validate();

        $this->dietPlan->meals()->create([
            'name'           => $this->name,
            'time'           => $this->time,
            'total_calories' => $this->total_calories,
            'protein_g'      => $this->protein_g,
            'carbs_g'        => $this->carbs_g,
            'fat_g'          => $this->fat_g,
            'meal_type'      => 'breakfast',
            'day_of_week'    => 'all',
            'foods'          => [],
            'sort_order'     => ($this->dietPlan->meals()->max('sort_order') ?? 0) + 1,
        ]);

        $this->reset(['name', 'time', 'total_calories', 'protein_g', 'carbs_g', 'fat_g']);
    }

    public function deleteMeal(int $mealId): void
    {
        abort_if($this->dietPlan->gym_id !== auth()->user()->gym_id, 403);

        DietMeal::where('id', $mealId)
            ->where('plan_id', $this->dietPlan->id)
            ->firstOrFail()
            ->delete();
    }

    public function render()
    {
        $meals      = $this->dietPlan->meals()->orderBy('sort_order')->get();
        $totalKcal  = $meals->sum('total_calories');
        $totalProt  = $meals->sum('protein_g');
        $totalCarbs = $meals->sum('carbs_g');
        $totalFat   = $meals->sum('fat_g');

        return view('livewire.diet-meals', compact('meals', 'totalKcal', 'totalProt', 'totalCarbs', 'totalFat'));
    }
}
