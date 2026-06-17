<?php

namespace Src\Service\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Shared\Support\Enums\AppointmentStatus;
use Src\Shared\Support\Traits\HasUuid;

class Appointment extends Model
{
    use HasUuid;

    protected $fillable = [
        'service_profile_id',
        'service_item_id',
        'master_id',
        'time_slot_id',
        'user_id',
        'client_name',
        'client_phone',
        'client_email',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_year',
        'vehicle_vin',
        'comment',
        'status',
        'estimated_cost',
        'internal_notes',
    ];

    protected $casts = [
        'status' => AppointmentStatus::class,
    ];

    public function serviceProfile(): BelongsTo
    {
        return $this->belongsTo(ServiceProfile::class);
    }

    public function serviceItem(): BelongsTo
    {
        return $this->belongsTo(ServiceItem::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(ServiceMaster::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
