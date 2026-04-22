<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class ClassBooking extends Model
{
    use BelongsToGym;

    protected $fillable = ['gym_id', 'class_id', 'member_id', 'booking_date', 'status'];

    protected $casts = ['booking_date' => 'date'];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function gymClass(): BelongsTo { return $this->belongsTo(GymClass::class, 'class_id'); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
