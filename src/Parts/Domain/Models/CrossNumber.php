<?php

namespace Src\Parts\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrossNumber extends Model
{
    protected $fillable = [
        'product_id',
        'brand',
        'number',
        'is_oem',
    ];

    protected $casts = [
        'is_oem' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
