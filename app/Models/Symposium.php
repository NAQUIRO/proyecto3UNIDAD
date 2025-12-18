<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Symposium extends Model
{
    protected $fillable = [
        'congress_id',
        'thematic_area_id',
        'title',
        'description',
        'slug',
        'start_time',
        'end_time',
        'moderator_name',
        'moderator_email',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($symposium) {
            if (empty($symposium->slug)) {
                $symposium->slug = Str::slug($symposium->title);
            }
        });
    }

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }

    public function thematicArea(): BelongsTo
    {
        return $this->belongsTo(ThematicArea::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(VirtualSession::class)->orderBy('scheduled_at');
    }
}
