<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class ContactDiary extends Model
{
    protected $table = 'contact_diary';

    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'contact_name', 'phone', 'email',
        'type', 'notes', 'follow_up_date', 'assigned_to', 'status',
    ];

    protected $casts = ['follow_up_date' => 'date'];

    public function gym(): BelongsTo        { return $this->belongsTo(Gym::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
}
