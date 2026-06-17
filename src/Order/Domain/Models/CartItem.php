<?php

namespace Src\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'itemable_type',
        'itemable_id',
        'quantity',
        'price',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
