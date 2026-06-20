<?php

namespace Src\Service\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class, 'category_id');
    }
}
