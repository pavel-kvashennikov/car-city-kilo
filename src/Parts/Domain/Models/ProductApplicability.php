<?php

namespace Src\Parts\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarModel;

class ProductApplicability extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'brand_id',
        'model_id',
        'year_from',
        'year_to',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }
}
