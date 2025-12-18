<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Paper extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'congress_id',
        'user_id',
        'thematic_area_id',
        'editorial_id',
        'title',
        'abstract',
        'keywords',
        'word_count',
        'word_limit',
        'status',
        'review_status',
        'plagiarism_suspected',
        'plagiarism_notes',
        'revision_notes',
        'rejection_reason',
        'video_url',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'word_count' => 'integer',
        'word_limit' => 'integer',
        'plagiarism_suspected' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thematicArea(): BelongsTo
    {
        return $this->belongsTo(ThematicArea::class);
    }

    public function editorial(): BelongsTo
    {
        return $this->belongsTo(Editorial::class);
    }

    public function coauthors(): HasMany
    {
        return $this->hasMany(PaperCoauthor::class)->orderBy('order');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PaperFile::class)->orderBy('created_at', 'desc');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function canAddCoauthor(): bool
    {
        return $this->coauthors()->count() < 3;
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted' || in_array($this->status, ['under_review', 'accepted', 'revision_required']);
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function requiresRevision(): bool
    {
        return $this->status === 'revision_required';
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function markAsPlagiarism(string $notes = null): void
    {
        $this->update([
            'plagiarism_suspected' => true,
            'plagiarism_notes' => $notes,
        ]);
    }

    public function getWordCountFromAbstract(): int
    {
        return str_word_count(strip_tags($this->abstract));
    }
}
