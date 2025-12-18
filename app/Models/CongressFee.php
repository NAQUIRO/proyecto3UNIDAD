<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongressFee extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'description',
        'amount',
        'currency',
        'start_date',
        'end_date',
        'user_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();
        return $now >= $this->start_date && $now <= $this->end_date;
    }

    public function appliesToUserType(string $userType): bool
    {
        return $this->user_type === 'both' || $this->user_type === $userType;
    }
}
