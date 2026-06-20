<?php

namespace Src\Service\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceItem extends Model
{
    protected $fillable = [
        'service_profile_id',
        'category_id',
        'name',
        'description',
        'price_fixed',
        'price_from',
        'price_to',
        'price_per_hour',
        'duration_minutes',
        'vehicle_types',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'vehicle_types' => 'array',
        'is_active'     => 'boolean',
    ];

    public function serviceProfile(): BelongsTo
    {
        return $this->belongsTo(ServiceProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
