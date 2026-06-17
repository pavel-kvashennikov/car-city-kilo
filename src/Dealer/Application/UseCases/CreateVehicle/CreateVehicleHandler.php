<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CreateVehicle;

use Src\Dealer\Domain\Contracts\VehicleRepositoryInterface;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Infrastructure\Search\MeilisearchService;

final class CreateVehicleHandler
{
    public function __construct(
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly MeilisearchService $search,
    ) {}

    public function handle(CreateVehicleCommand $command): Vehicle
    {
        $vehicle = $this->vehicles->create([
            'dealer_profile_id' => $command->dealerProfileId,
            'brand_id' => $command->brandId,
            'model_id' => $command->modelId,
            'generation_id' => $command->generationId,
            'year' => $command->year,
            'vin' => $command->vin,
            'mileage' => $command->mileage,
            'price' => $command->price->getAmount(),
            'color' => $command->color,
            'engine_type' => $command->engineType,
            'engine_volume' => $command->engineVolume,
            'engine_power' => $command->enginePower,
            'transmission' => $command->transmission,
            'drive_type' => $command->driveType,
            'body_type' => $command->bodyType,
            'condition' => $command->condition ?? 'used',
            'description' => $command->description,
            'attributes' => $command->attributes,
            'status' => 'draft',
        ]);

        return $vehicle;
    }
}
