<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietMeal extends Model
{
    protected $fillable = [
        'plan_id', 'name', 'time', 'meal_type', 'day_of_week',
        'foods', 'total_calories', 'protein_g', 'carbs_g', 'fat_g', 'sort_order',
    ];

    protected $casts = [
        'foods'          => 'array',
        'total_calories' => 'integer',
    ];

    public function plan(): BelongsTo { return $this->belongsTo(DietPlan::class, 'plan_id'); }
}
