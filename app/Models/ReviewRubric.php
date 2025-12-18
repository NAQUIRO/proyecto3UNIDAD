<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRubric extends Model
{
    protected $fillable = [
        'review_id',
        'criterion',
        'description',
        'score',
        'max_score',
        'comments',
        'order',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'order' => 'integer',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->max_score == 0) {
            return 0;
        }
        return ($this->score / $this->max_score) * 100;
    }
}
