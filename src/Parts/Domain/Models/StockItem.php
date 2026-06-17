<?php

declare(strict_types=1);

namespace Src\Parts\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockItem extends Model
{
    protected $fillable = [
        'product_id',
        'parts_profile_id',
        'warehouse_name',
        'quantity',
        'reserved_quantity',
        'min_quantity',
        'location',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function partsProfile(): BelongsTo
    {
        return $this->belongsTo(PartsProfile::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    public function isInStock(): bool
    {
        return $this->available_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->min_quantity;
    }
}
