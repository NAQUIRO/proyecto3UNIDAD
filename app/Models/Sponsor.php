<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Sponsor extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'description',
        'logo',
        'website',
        'email',
        'phone',
        'sponsor_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Optimización: Eager loading por defecto
    protected $with = ['congress'];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        return asset('storage/' . $this->logo);
    }

    public function getSponsorTypeLabelAttribute(): string
    {
        return match ($this->sponsor_type) {
            'platinum' => 'Platino',
            'gold' => 'Oro',
            'silver' => 'Plata',
            'bronze' => 'Bronce',
            'partner' => 'Socio',
            default => 'Socio',
        };
    }

    // Optimización: Scope para activos
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Optimización: Scope para tipo
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('sponsor_type', $type);
    }
}
