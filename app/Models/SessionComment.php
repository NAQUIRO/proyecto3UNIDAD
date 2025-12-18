<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionComment extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'author_name',
        'author_email',
        'content',
        'type',
        'parent_id',
        'is_answered',
        'is_approved',
        'likes_count',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
        'is_approved' => 'boolean',
        'likes_count' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(VirtualSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SessionComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SessionComment::class, 'parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'asc');
    }

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function markAsAnswered(): void
    {
        $this->update(['is_answered' => true]);
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }
}
