<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ThematicArea extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thematicArea) {
            if (empty($thematicArea->slug)) {
                $thematicArea->slug = Str::slug($thematicArea->name);
            }
        });
    }

    public function congresses(): BelongsToMany
    {
        return $this->belongsToMany(Congress::class, 'congress_thematic_area');
    }
}
