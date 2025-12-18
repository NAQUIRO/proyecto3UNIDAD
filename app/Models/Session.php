<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Session extends Model
{
    protected $fillable = [
        'congress_id',
        'symposium_id',
        'paper_id',
        'title',
        'description',
        'presenter_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'video_url',
        'presentation_url',
        'type',
        'status',
        'views_count',
        'sort_order',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'views_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function symposium(): BelongsTo
    {
        return $this->belongsTo(Symposium::class);
    }

    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function presenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'presenter_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
