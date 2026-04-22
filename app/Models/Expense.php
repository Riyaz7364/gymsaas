<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class Expense extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'finance_type_id', 'title', 'amount', 'date',
        'description', 'receipt_photo', 'added_by',
    ];

    protected $casts = ['date' => 'date'];

    public function gym(): BelongsTo         { return $this->belongsTo(Gym::class); }
    public function financeType(): BelongsTo { return $this->belongsTo(FinanceType::class); }
    public function addedBy(): BelongsTo     { return $this->belongsTo(User::class, 'added_by'); }
}
