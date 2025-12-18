<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbstractBook extends Model
{
    protected $fillable = [
        'congress_id',
        'created_by',
        'title',
        'description',
        'file_path',
        'status',
        'papers_count',
        'included_papers',
        'generation_notes',
        'generated_at',
        'published_at',
    ];

    protected $casts = [
        'included_papers' => 'array',
        'papers_count' => 'integer',
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

    public function isGenerated(): bool
    {
        return $this->status === 'completed' && $this->file_path !== null;
    }

    public function markAsGenerated(string $filePath, int $papersCount, array $includedPapers): void
    {
        $this->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'papers_count' => $papersCount,
            'included_papers' => $includedPapers,
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

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
