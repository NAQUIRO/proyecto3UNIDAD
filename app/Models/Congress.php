<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Congress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'logo',
        'banner',
        'start_date',
        'end_date',
        'url',
        'status',
        'meta_description',
        'meta_keywords',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($congress) {
            if (empty($congress->slug)) {
                $congress->slug = Str::slug($congress->title);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function thematicAreas(): BelongsToMany
    {
        return $this->belongsToMany(ThematicArea::class, 'congress_thematic_area');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'congress_user')
            ->withPivot('role', 'registration_status', 'registration_fee', 'payment_status', 'registered_at')
            ->withTimestamps();
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(CongressMilestone::class)->orderBy('deadline');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(CongressFee::class)->orderBy('sort_order');
    }

    public function activeFees(): HasMany
    {
        return $this->fees()->where('is_active', true)
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }

    public function getCurrentFeeForUserType(string $userType): ?CongressFee
    {
        return $this->activeFees()
            ->where(function($query) use ($userType) {
                $query->where('user_type', 'both')
                      ->orWhere('user_type', $userType);
            })
            ->orderBy('sort_order')
            ->first();
    }

    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class);
    }

    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'congress_user')
            ->wherePivot('role', 'reviewer')
            ->withTimestamps();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function symposia(): HasMany
    {
        return $this->hasMany(Symposium::class)->orderBy('sort_order');
    }

    public function virtualSessions(): HasMany
    {
        return $this->hasMany(VirtualSession::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function certificateTemplates(): HasMany
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function bookOfAbstracts(): HasMany
    {
        return $this->hasMany(BookOfAbstracts::class);
    }
}
