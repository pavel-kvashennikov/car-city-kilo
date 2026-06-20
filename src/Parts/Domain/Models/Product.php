<?php

namespace Src\Parts\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Src\Catalog\Domain\Models\PartCategory;
use Src\Shared\Support\Traits\HasUuid;

class Product extends Model
{
    use HasSlug, HasUuid, SoftDeletes;

    protected $fillable = [
        'parts_profile_id',
        'category_id',
        'name',
        'article_number',
        'oem_number',
        'barcode',
        'brand',
        'condition',
        'part_type',
        'price_retail',
        'price_wholesale',
        'wholesale_min_qty',
        'description',
        'attributes',
        'status',
        'seo_title',
        'seo_description',
        'stock_quantity',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price_retail' => 'integer',
        'price_wholesale' => 'integer',
    ];

    protected $appends = ['cover_photo_url', 'images'];

    public function getImagesAttribute(): array
    {
        return data_get($this->getAttribute('attributes'), 'images', []);
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function partsProfile(): BelongsTo
    {
        return $this->belongsTo(PartsProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartCategory::class);
    }

    public function crossNumbers(): HasMany
    {
        return $this->hasMany(CrossNumber::class);
    }

    public function applicabilities(): HasMany
    {
        return $this->hasMany(ProductApplicability::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price_retail, 0, '.', ' ').' ₽';
    }
}
