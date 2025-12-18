<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingTier extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'price',
        'currency',
        'user_type',
        'max_uses',
        'current_uses',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
        'current_uses' => 'integer',
        'sort_order' => 'integer',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function isAvailableForUserType(string $userType): bool
    {
        return $this->user_type === 'both' || $this->user_type === $userType;
    }

    public function incrementUses(): void
    {
        $this->increment('current_uses');
    }
}
