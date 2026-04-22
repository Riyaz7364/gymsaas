<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class FinanceType extends Model
{
    use BelongsToGym;
    protected $fillable = ['gym_id', 'name', 'type'];
    public function gym(): BelongsTo      { return $this->belongsTo(Gym::class); }
    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Expense::class); }
}

