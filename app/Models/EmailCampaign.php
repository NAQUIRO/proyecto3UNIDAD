<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $fillable = [
        'congress_id',
        'created_by',
        'subject',
        'content',
        'status',
        'segment_type',
        'segment_filters',
        'total_recipients',
        'sent_count',
        'failed_count',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'segment_filters' => 'array',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'campaign_id');
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_recipients == 0) {
            return 0;
        }
        return ($this->sent_count / $this->total_recipients) * 100;
    }
}
