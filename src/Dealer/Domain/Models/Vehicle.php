<?php

namespace Src\Dealer\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarGeneration;
use Src\Catalog\Domain\Models\CarModel;
use Src\Shared\Support\Enums\VehicleStatus;
use Src\Shared\Support\Traits\HasUuid;

class Vehicle extends Model
{
    use HasSlug, HasUuid, SoftDeletes;

    protected $fillable = [
        'dealer_profile_id',
        'brand_id',
        'model_id',
        'generation_id',
        'year',
        'vin',
        'mileage',
        'color',
        'engine_type',
        'engine_volume',
        'engine_power',
        'transmission',
        'drive_type',
        'body_type',
        'condition',
        'legal_status',
        'price',
        'price_trade_in',
        'credit_monthly',
        'description',
        'features',
        'attributes',
        'status',
        'seo_title',
        'seo_description',
        'is_featured',
    ];

    protected $casts = [
        'features' => 'array',
        'attributes' => 'array',
        'status' => VehicleStatus::class,
        'is_featured' => 'boolean',
        'price' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return implode(' ', array_filter([
                    $model->brand?->name,
                    $model->carModel?->name,
                    $model->year,
                ]));
            })
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(200);
    }

    public function dealerProfile(): BelongsTo
    {
        return $this->belongsTo(DealerProfile::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(CarGeneration::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('sort_order');
    }

    public function mainPhoto(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->where('is_main', true);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(DealerLead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', VehicleStatus::ACTIVE);
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price, 0, '.', ' ').' ₽';
    }
}
