<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class MemberAiMessage extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'attendance_id',
        'ai_context', 'ai_response', 'whatsapp_body', 'sent_via_whatsapp',
    ];

    protected $casts = [
        'sent_via_whatsapp' => 'boolean',
    ];

    public function gym(): BelongsTo        { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo     { return $this->belongsTo(Member::class); }
    public function attendance(): BelongsTo { return $this->belongsTo(Attendance::class); }
}
