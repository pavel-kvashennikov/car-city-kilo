<?php

namespace Src\Company\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Shared\Support\Enums\BusinessProfileType;

class BusinessProfile extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'is_active',
    ];

    protected $casts = [
        'type' => BusinessProfileType::class,
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
