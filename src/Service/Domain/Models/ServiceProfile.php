<?php

namespace Src\Service\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Domain\Models\Company;

class ServiceProfile extends Model
{
    protected $fillable = [
        'company_id',
        'slug',
        'description',
        'working_hours',
        'specializations',
        'vehicle_types',
        'slot_duration_minutes',
        'meta',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'specializations' => 'array',
        'vehicle_types' => 'array',
        'meta' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    public function masters(): HasMany
    {
        return $this->hasMany(ServiceMaster::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
