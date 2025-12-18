<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Speaker extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'email',
        'phone',
        'bio',
        'photo',
        'institution',
        'position',
        'specialization',
        'country',
        'website',
        'social_media',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'social_media' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Optimización: Eager loading por defecto
    protected $with = ['congress'];

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function papers(): BelongsToMany
    {
        return $this->belongsToMany(Paper::class, 'paper_speakers')
            ->withTimestamps();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        return asset('storage/' . $this->photo);
    }

    // Optimización: Scope para activos
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Optimización: Scope para destacados
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
