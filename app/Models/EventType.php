<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class EventType extends Model
{
    use BelongsToGym;
    protected $fillable = ['gym_id', 'name', 'color'];
    public function gym(): BelongsTo { return $this->belongsTo(Gym::class); }
}
