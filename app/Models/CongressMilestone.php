<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongressMilestone extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'description',
        'deadline',
        'blocks_actions',
        'type',
        'is_active',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'blocks_actions' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function isPast(): bool
    {
        return now()->isAfter($this->deadline);
    }

    public function isActive(): bool
    {
        return $this->is_active && !$this->isPast();
    }

    public function shouldBlockActions(): bool
    {
        return $this->blocks_actions && $this->isPast();
    }
}
