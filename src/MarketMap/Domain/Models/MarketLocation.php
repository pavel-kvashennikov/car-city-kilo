<?php

namespace Src\MarketMap\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Domain\Models\Company;

class MarketLocation extends Model
{
    protected $fillable = [
        'zone_id',
        'company_id',
        'code',
        'type',
        'status',
        'coordinates',
        'description',
    ];

    protected $casts = [
        'coordinates' => 'array',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(MarketZone::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
