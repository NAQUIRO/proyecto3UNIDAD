<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialDownload extends Model
{
    protected $fillable = [
        'congress_id',
        'editorial_id',
        'downloaded_by',
        'zip_path',
        'files_count',
        'total_size',
        'status',
        'generated_at',
        'downloaded_at',
        'expires_at',
    ];

    protected $casts = [
        'files_count' => 'integer',
        'total_size' => 'integer',
        'generated_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function editorial(): BelongsTo
    {
        return $this->belongsTo(Editorial::class);
    }

    public function downloadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function markAsReady(): void
    {
        $this->update([
            'status' => 'ready',
            'generated_at' => now(),
            'expires_at' => now()->addDays(7), // Expira en 7 días
        ]);
    }

    public function markAsDownloaded(): void
    {
        $this->update([
            'status' => 'downloaded',
            'downloaded_at' => now(),
        ]);
    }

    public function getZipUrlAttribute(): ?string
    {
        if (!$this->zip_path) {
            return null;
        }
        return asset('storage/' . $this->zip_path);
    }

    public function getTotalSizeHumanAttribute(): string
    {
        $bytes = $this->total_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
