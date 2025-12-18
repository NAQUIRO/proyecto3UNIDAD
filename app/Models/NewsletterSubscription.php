<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'email',
        'name',
        'privacy_accepted',
        'verified_at',
        'verification_token',
        'is_active',
    ];

    protected $casts = [
        'privacy_accepted' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->verification_token)) {
                $subscription->verification_token = Str::random(60);
            }
        });
    }

    public function verify(): void
    {
        $this->update([
            'verified_at' => now(),
            'is_active' => true,
        ]);
    }
}
