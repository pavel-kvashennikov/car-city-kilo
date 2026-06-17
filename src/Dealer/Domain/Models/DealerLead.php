<?php

namespace Src\Dealer\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Shared\Support\Enums\LeadType;
use Src\Shared\Support\Traits\HasUuid;

class DealerLead extends Model
{
    use HasUuid;

    protected $fillable = [
        'dealer_profile_id',
        'vehicle_id',
        'user_id',
        'lead_type',
        'client_name',
        'client_phone',
        'client_email',
        'preferred_datetime',
        'trade_in_data',
        'credit_data',
        'message',
        'status',
        'notes',
        'assigned_to',
    ];

    protected $casts = [
        'lead_type' => LeadType::class,
        'trade_in_data' => 'array',
        'credit_data' => 'array',
        'preferred_datetime' => 'datetime',
    ];

    public function dealerProfile(): BelongsTo
    {
        return $this->belongsTo(DealerProfile::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
