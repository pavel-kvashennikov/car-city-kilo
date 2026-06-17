<?php

namespace Src\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarBrand extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
