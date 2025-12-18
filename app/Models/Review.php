<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'review_assignment_id',
        'status',
        'recommendation',
        'comments',
        'confidential_comments',
        'overall_score',
        'is_blind_review',
        'assigned_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'is_blind_review' => 'boolean',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ReviewAssignment::class, 'review_assignment_id');
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(ReviewRubric::class)->orderBy('order');
    }

    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(string $recommendation, array $data = []): void
    {
        $this->update(array_merge([
            'status' => 'completed',
            'recommendation' => $recommendation,
            'completed_at' => now(),
        ], $data));
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function calculateOverallScore(): float
    {
        $rubrics = $this->rubrics;
        if ($rubrics->isEmpty()) {
            return 0;
        }

        $totalScore = $rubrics->sum('score');
        $maxScore = $rubrics->sum('max_score');
        
        return $maxScore > 0 ? ($totalScore / $maxScore) * 10 : 0; // Normalizado a 10
    }
}
