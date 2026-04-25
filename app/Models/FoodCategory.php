<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class FoodCategory extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'name', 'description', 'icon',
    ];

    public function gym(): BelongsTo       { return $this->belongsTo(Gym::class); }
    public function foodItems(): HasMany   { return $this->hasMany(FoodItem::class, 'category_id')->orderBy('name'); }
}
