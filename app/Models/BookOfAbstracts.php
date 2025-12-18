<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOfAbstracts extends Model
{
    protected $fillable = [
        'congress_id',
        'created_by',
        'title',
        'description',
        'pdf_path',
        'cover_image',
        'status',
        'total_papers',
        'included_papers',
        'settings',
        'generated_at',
        'published_at',
    ];

    protected $casts = [
        'total_papers' => 'integer',
        'included_papers' => 'array',
        'settings' => 'array',
        'generated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'generated_at' => now(),
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }
        return asset('storage/' . $this->pdf_path);
    }
}
