<?php

namespace Src\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'is_popular',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function generations(): HasMany
    {
        return $this->hasMany(CarGeneration::class, 'model_id');
    }
}
