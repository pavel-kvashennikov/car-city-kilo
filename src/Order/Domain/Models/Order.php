<?php

namespace Src\Order\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Shared\Support\Enums\OrderStatus;
use Src\Shared\Support\Traits\HasUuid;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_number',
        'user_id',
        'client_name',
        'client_phone',
        'client_email',
        'status',
        'total_amount',
        'delivery_method',
        'delivery_address',
        'comment',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'delivery_address' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
