<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class WorkoutCategory extends Model
{
    use BelongsToGym;

    protected $fillable = ['gym_id', 'name', 'icon'];

    public function gym(): BelongsTo        { return $this->belongsTo(Gym::class); }
    public function activities(): HasMany   { return $this->hasMany(WorkoutActivity::class, 'category_id'); }
}
