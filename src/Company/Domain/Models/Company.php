<?php

namespace Src\Company\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Src\Dealer\Domain\Models\DealerProfile;
use Src\MarketMap\Domain\Models\MarketLocation;
use Src\Parts\Domain\Models\PartsProfile;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Shared\Support\Enums\CompanyStatus;
use Src\Shared\Support\Traits\HasUuid;

class Company extends Model
{
    use HasSlug, HasUuid, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'inn',
        'legal_name',
        'description',
        'logo_path',
        'phone',
        'email',
        'website',
        'working_hours',
        'social_links',
        'status',
        'reject_reason',
        'verified_at',
        'meta',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'social_links' => 'array',
        'meta' => 'array',
        'status' => CompanyStatus::class,
        'verified_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function dealerProfile(): HasOne
    {
        return $this->hasOne(DealerProfile::class);
    }

    public function partsProfile(): HasOne
    {
        return $this->hasOne(PartsProfile::class);
    }

    public function serviceProfile(): HasOne
    {
        return $this->hasOne(ServiceProfile::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(MarketLocation::class);
    }

    public function isActive(): bool
    {
        return $this->status === CompanyStatus::ACTIVE;
    }

    public function getActiveProfilesAttribute(): array
    {
        return $this->businessProfiles->map(fn ($p) => [
            'type' => $p->type?->value ?? (string) $p->type,
            'id' => $p->id,
        ])->toArray();
    }
}
