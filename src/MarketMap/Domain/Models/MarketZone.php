<?php

namespace Src\MarketMap\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketZone extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'svg_path',
        'sort_order',
    ];

    protected $casts = [
        'svg_path' => 'array',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(MarketLocation::class, 'zone_id');
    }
}
