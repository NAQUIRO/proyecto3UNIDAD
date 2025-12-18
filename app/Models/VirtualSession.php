<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualSession extends Model
{
    protected $fillable = [
        'congress_id',
        'symposium_id',
        'paper_id',
        'title',
        'description',
        'video_url',
        'video_provider',
        'video_id',
        'duration_minutes',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
        'views_count',
        'comments_count',
        'is_live',
        'is_recorded',
        'presenter_notes',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'views_count' => 'integer',
        'comments_count' => 'integer',
        'is_live' => 'boolean',
        'is_recorded' => 'boolean',
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

    public function comments(): HasMany
    {
        return $this->hasMany(SessionComment::class, 'session_id')
            ->where('parent_id', null)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(SessionComment::class, 'session_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SessionComment::class, 'session_id')
            ->where('type', 'question')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementComments(): void
    {
        $this->increment('comments_count');
    }

    public function start(): void
    {
        $this->update([
            'status' => 'live',
            'is_live' => true,
            'started_at' => now(),
        ]);
    }

    public function end(): void
    {
        $this->update([
            'status' => 'completed',
            'is_live' => false,
            'ended_at' => now(),
        ]);
    }

    public function getVideoEmbedUrlAttribute(): string
    {
        return match ($this->video_provider) {
            'youtube' => "https://www.youtube.com/embed/{$this->video_id}",
            'vimeo' => "https://player.vimeo.com/video/{$this->video_id}",
            default => $this->video_url,
        };
    }
}
