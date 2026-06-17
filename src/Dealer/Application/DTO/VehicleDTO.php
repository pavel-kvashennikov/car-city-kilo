<?php

declare(strict_types=1);

namespace Src\Dealer\Application\DTO;

use Illuminate\Http\Request;

final class VehicleDTO
{
    public function __construct(
        public readonly int $dealerProfileId,
        public readonly ?int $brandId = null,
        public readonly ?int $modelId = null,
        public readonly ?int $generationId = null,
        public readonly ?int $year = null,
        public readonly ?string $vin = null,
        public readonly ?int $mileage = null,
        public readonly ?string $color = null,
        public readonly ?string $engineType = null,
        public readonly ?float $engineVolume = null,
        public readonly ?int $enginePower = null,
        public readonly ?string $transmission = null,
        public readonly ?string $driveType = null,
        public readonly ?string $bodyType = null,
        public readonly ?string $condition = null,
        public readonly ?int $price = null,
        public readonly ?string $description = null,
        public readonly ?array $features = null,
        public readonly ?array $attributes = null,
    ) {}

    public static function fromRequest(Request $request, int $dealerProfileId): self
    {
        return new self(
            dealerProfileId: $dealerProfileId,
            brandId: $request->input('brand_id'),
            modelId: $request->input('model_id'),
            generationId: $request->input('generation_id'),
            year: $request->input('year'),
            vin: $request->input('vin'),
            mileage: $request->input('mileage'),
            color: $request->input('color'),
            engineType: $request->input('engine_type'),
            engineVolume: $request->input('engine_volume'),
            enginePower: $request->input('engine_power'),
            transmission: $request->input('transmission'),
            driveType: $request->input('drive_type'),
            bodyType: $request->input('body_type'),
            condition: $request->input('condition'),
            price: $request->input('price'),
            description: $request->input('description'),
            features: $request->input('features'),
            attributes: $request->input('attributes'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'dealer_profile_id' => $this->dealerProfileId,
            'brand_id' => $this->brandId,
            'model_id' => $this->modelId,
            'generation_id' => $this->generationId,
            'year' => $this->year,
            'vin' => $this->vin,
            'mileage' => $this->mileage,
            'color' => $this->color,
            'engine_type' => $this->engineType,
            'engine_volume' => $this->engineVolume,
            'engine_power' => $this->enginePower,
            'transmission' => $this->transmission,
            'drive_type' => $this->driveType,
            'body_type' => $this->bodyType,
            'condition' => $this->condition,
            'price' => $this->price,
            'description' => $this->description,
            'features' => $this->features,
            'attributes' => $this->attributes,
        ], fn ($value) => $value !== null);
    }
}
