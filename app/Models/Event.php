<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class Event extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'event_type_id', 'created_by',
        'title', 'description', 'start_datetime', 'end_datetime',
        'all_day', 'color', 'location',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'all_day'        => 'boolean',
    ];

    public function gym(): BelongsTo       { return $this->belongsTo(Gym::class); }
    public function eventType(): BelongsTo { return $this->belongsTo(EventType::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
