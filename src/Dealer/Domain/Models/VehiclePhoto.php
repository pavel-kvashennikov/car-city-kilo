<?php

namespace Src\Dealer\Domain\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePhoto extends Model
{
    protected $fillable = [
        'vehicle_id',
        'path',
        'thumb_path',
        'sort_order',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    protected $appends = ['url', 'thumb_url'];

    public function getUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->path);
    }

    public function getThumbUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->thumb_path ?? $this->path);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
