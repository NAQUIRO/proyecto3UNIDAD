<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateValidation extends Model
{
    protected $fillable = [
        'certificate_id',
        'requirement_type',
        'is_met',
        'notes',
        'validated_at',
    ];

    protected $casts = [
        'is_met' => 'boolean',
        'validated_at' => 'datetime',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function markAsMet(string $notes = null): void
    {
        $this->update([
            'is_met' => true,
            'notes' => $notes,
            'validated_at' => now(),
        ]);
    }

    public function markAsNotMet(string $notes = null): void
    {
        $this->update([
            'is_met' => false,
            'notes' => $notes,
            'validated_at' => now(),
        ]);
    }
}
