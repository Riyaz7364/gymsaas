<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyStatPhoto extends Model
{
    protected $fillable = [
        'body_stat_id',
        'photo_path',
    ];

    public function bodyStat(): BelongsTo
    {
        return $this->belongsTo(BodyStat::class);
    }
}