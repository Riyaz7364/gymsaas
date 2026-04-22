<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class WorkoutActivity extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'category_id', 'name', 'description',
        'muscle_group', 'equipment', 'video_url', 'image', 'difficulty',
    ];

    public function gym(): BelongsTo      { return $this->belongsTo(Gym::class); }
    public function category(): BelongsTo { return $this->belongsTo(WorkoutCategory::class, 'category_id'); }
}
