<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class WhatsappCampaign extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'name', 'target_filter', 'template', 'body',
        'scheduled_at', 'status', 'total_count', 'sent_count', 'failed_count', 'created_by',
    ];

    protected $casts = [
        'target_filter' => 'array',
        'scheduled_at'  => 'datetime',
    ];

    public function gym(): BelongsTo       { return $this->belongsTo(Gym::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
