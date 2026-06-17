<?php

namespace Src\Parts\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Domain\Models\Company;

class PartsProfile extends Model
{
    protected $fillable = [
        'company_id',
        'description',
        'working_hours',
        'min_order_amount',
        'delivery_info',
        'payment_methods',
        'meta',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'delivery_info' => 'array',
        'payment_methods' => 'array',
        'meta' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
