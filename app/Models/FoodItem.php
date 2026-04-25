<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class FoodItem extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'category_id', 'name', 'serving_size', 'serving_unit',
        'calories', 'protein_g', 'carbs_g', 'fat_g', 'fiber_g',
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein_g' => 'decimal:2',
        'carbs_g' => 'decimal:2',
        'fat_g' => 'decimal:2',
        'fiber_g' => 'decimal:2',
    ];

    public function gym(): BelongsTo       { return $this->belongsTo(Gym::class); }
    public function category(): BelongsTo  { return $this->belongsTo(FoodCategory::class, 'category_id'); }
}
