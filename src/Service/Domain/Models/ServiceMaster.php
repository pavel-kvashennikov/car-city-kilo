<?php

namespace Src\Service\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMaster extends Model
{
    protected $fillable = [
        'service_profile_id',
        'name',
        'specialization',
        'photo_path',
        'schedule',
        'is_active',
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_active' => 'boolean',
    ];

    public function serviceProfile(): BelongsTo
    {
        return $this->belongsTo(ServiceProfile::class);
    }
}
