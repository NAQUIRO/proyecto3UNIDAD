<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperCoauthor extends Model
{
    protected $fillable = [
        'paper_id',
        'user_id',
        'name',
        'email',
        'affiliation',
        'order',
        'is_registered_user',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_registered_user' => 'boolean',
    ];

    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
}
