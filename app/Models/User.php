<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function congresses(): BelongsToMany
    {
        return $this->belongsToMany(Congress::class, 'congress_user')
            ->withPivot('role', 'registration_status', 'payment_status', 'amount_paid', 'registered_at', 'confirmed_at', 'notes')
            ->withTimestamps();
    }

    public function billingData(): HasMany
    {
        return $this->hasMany(UserBillingData::class);
    }

    public function defaultBillingData(): ?UserBillingData
    {
        return $this->billingData()->where('is_default', true)->first();
    }

    public function impersonationLogsAsAdmin(): HasMany
    {
        return $this->hasMany(ImpersonationLog::class, 'admin_id');
    }

    public function impersonationLogsAsUser(): HasMany
    {
        return $this->hasMany(ImpersonationLog::class, 'user_id');
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class);
    }

    public function coauthoredPapers(): BelongsToMany
    {
        return $this->belongsToMany(Paper::class, 'paper_coauthors')
            ->withPivot('name', 'email', 'affiliation', 'order', 'is_registered_user')
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    public function isRegisteredInCongress(int $congressId): bool
    {
        return $this->congresses()->where('congress_id', $congressId)->exists();
    }

    public function getRoleInCongress(int $congressId): ?string
    {
        $pivot = $this->congresses()->where('congress_id', $congressId)->first();
        return $pivot?->pivot->role;
    }

    public function isReviewerForCongress(int $congressId): bool
    {
        return $this->congresses()
            ->where('congress_id', $congressId)
            ->wherePivot('role', 'reviewer')
            ->exists();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class, 'created_by');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
