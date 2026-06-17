<?php

namespace Src\Dealer\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Domain\Models\Company;

class DealerProfile extends Model
{
    protected $fillable = [
        'company_id',
        'description',
        'working_hours',
        'specializations',
        'offers_credit',
        'offers_trade_in',
        'offers_test_drive',
        'meta',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'specializations' => 'array',
        'meta' => 'array',
        'offers_credit' => 'boolean',
        'offers_trade_in' => 'boolean',
        'offers_test_drive' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(DealerLead::class);
    }
}
