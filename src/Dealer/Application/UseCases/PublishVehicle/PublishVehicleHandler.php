<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\PublishVehicle;

use Src\Dealer\Domain\Contracts\VehicleRepositoryInterface;
use Src\Dealer\Domain\Events\VehiclePublished;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Support\Enums\VehicleStatus;

final class PublishVehicleHandler
{
    public function __construct(
        private readonly VehicleRepositoryInterface $vehicles,
    ) {}

    public function handle(PublishVehicleCommand $command): Vehicle
    {
        $vehicle = $this->vehicles->find($command->vehicleId);

        $this->vehicles->update($vehicle, [
            'status' => VehicleStatus::ACTIVE,
            'published_at' => now(),
        ]);

        $vehicle->refresh();

        event(new VehiclePublished($vehicle));

        return $vehicle;
    }
}
