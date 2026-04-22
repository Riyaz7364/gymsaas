<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class WhatsappLog extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'to_phone', 'direction', 'template', 'trigger',
        'body', 'status', 'whatsapp_message_id', 'meta_response', 'error_message', 'sent_at',
    ];

    protected $casts = [
        'sent_at'       => 'datetime',
        'meta_response' => 'array',
    ];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
