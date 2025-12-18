<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VirtualRoom extends Model
{
    protected $fillable = [
        'congress_id',
        'name',
        'slug',
        'platform',
        'room_url',
        'room_id',
        'password',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->slug)) {
                $room->slug = Str::slug($room->name);
            }
        });
    }

    public function congress(): BelongsTo
    {
        return $this->belongsTo(Congress::class);
    }
}
