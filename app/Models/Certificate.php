<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'congress_id',
        'user_id',
        'template_id',
        'type',
        'certificate_number',
        'pdf_path',
        'status',
        'validation_notes',
        'requirements_met',
        'requirements_failed',
        'is_valid',
        'generated_at',
        'issued_at',
    ];

    protected $casts = [
        'requirements_met' => 'array',
        'requirements_failed' => 'array',
        'is_valid' => 'boolean',
        'generated_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = self::generateCertificateNumber();
            }
        });
    }

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function isGenerated(): bool
    {
        return $this->status === 'generated' || $this->status === 'issued';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function markAsGenerated(): void
    {
        $this->update([
            'status' => 'generated',
            'generated_at' => now(),
        ]);
    }

    public function markAsIssued(): void
    {
        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
        ]);
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }
        return asset('storage/' . $this->pdf_path);
    }

    public static function generateCertificateNumber(): string
    {
        return 'CERT-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
}
