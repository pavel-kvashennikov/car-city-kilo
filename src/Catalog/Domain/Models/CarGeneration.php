<?php

namespace Src\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarGeneration extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model_id',
        'name',
        'year_from',
        'year_to',
        'body_type',
    ];

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }
}
