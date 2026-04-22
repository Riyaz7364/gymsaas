<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSetting extends Model
{
    protected $fillable = ['gym_id', 'key', 'value'];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
