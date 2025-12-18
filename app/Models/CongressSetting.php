<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongressSetting extends Model
{
    protected $fillable = [
        'congress_id',
        'registration_start_date',
        'registration_end_date',
        'paper_submission_start_date',
        'paper_submission_end_date',
        'review_start_date',
        'review_end_date',
        'notification_date',
        'final_paper_deadline',
        'allow_registration',
        'allow_paper_submission',
        'allow_review',
        'registration_instructions',
        'paper_submission_instructions',
    ];

    protected $casts = [
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'paper_submission_start_date' => 'date',
        'paper_submission_end_date' => 'date',
        'review_start_date' => 'date',
        'review_end_date' => 'date',
        'notification_date' => 'date',
        'final_paper_deadline' => 'date',
        'allow_registration' => 'boolean',
        'allow_paper_submission' => 'boolean',
        'allow_review' => 'boolean',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function isRegistrationOpen(): bool
    {
        if (!$this->allow_registration) {
            return false;
        }

        $now = now();
        if ($this->registration_start_date && $now->lt($this->registration_start_date)) {
            return false;
        }
        if ($this->registration_end_date && $now->gt($this->registration_end_date)) {
            return false;
        }

        return true;
    }

    public function isPaperSubmissionOpen(): bool
    {
        if (!$this->allow_paper_submission) {
            return false;
        }

        $now = now();
        if ($this->paper_submission_start_date && $now->lt($this->paper_submission_start_date)) {
            return false;
        }
        if ($this->paper_submission_end_date && $now->gt($this->paper_submission_end_date)) {
            return false;
        }

        return true;
    }

    public function isReviewOpen(): bool
    {
        if (!$this->allow_review) {
            return false;
        }

        $now = now();
        if ($this->review_start_date && $now->lt($this->review_start_date)) {
            return false;
        }
        if ($this->review_end_date && $now->gt($this->review_end_date)) {
            return false;
        }

        return true;
    }
}
