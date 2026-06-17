<?php

declare(strict_types=1);

namespace Src\Company\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyLocation extends Model
{
    protected $table = 'market_locations';

    protected $fillable = [
        'zone_id',
        'company_id',
        'business_profile_id',
        'identifier',
        'location_type',
        'floor',
        'coordinates',
        'geo_coordinates',
        'status',
        'area_sqm',
        'notes',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'geo_coordinates' => 'array',
        'area_sqm' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function isAvailable(): bool
    {
        return $this->company_id === null;
    }

    public function isOccupied(): bool
    {
        return $this->company_id !== null;
    }
}
