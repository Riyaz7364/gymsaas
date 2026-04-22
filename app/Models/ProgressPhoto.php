<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class ProgressPhoto extends Model
{
    use BelongsToGym;

    protected $fillable = ['gym_id', 'member_id', 'before_photo', 'after_photo', 'date', 'notes'];

    protected $casts = ['date' => 'date'];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
