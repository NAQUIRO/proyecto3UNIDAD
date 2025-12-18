<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editorial extends Model
{
    protected $fillable = [
        'name',
        'description',
        'contact_email',
        'contact_phone',
        'website',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class);
    }
}
