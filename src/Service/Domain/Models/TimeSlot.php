<?php

namespace Src\Service\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Shared\Support\Enums\SlotStatus;

class TimeSlot extends Model
{
    protected $fillable = [
        'service_profile_id',
        'master_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'appointment_id',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => SlotStatus::class,
    ];

    public function serviceProfile(): BelongsTo
    {
        return $this->belongsTo(ServiceProfile::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(ServiceMaster::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === SlotStatus::AVAILABLE;
    }
}
