<?php

declare(strict_types=1);

namespace Src\Billing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Domain\Models\Company;

class Payment extends Model
{
    protected $fillable = [
        'company_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'external_id',
        'status',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
