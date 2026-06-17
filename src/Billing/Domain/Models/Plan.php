<?php

namespace Src\Billing\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'features',
        'allowed_profiles',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'allowed_profiles' => 'array',
        'is_active' => 'boolean',
    ];
}
