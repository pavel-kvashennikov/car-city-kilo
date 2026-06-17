<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\PublishVehicle;

final class PublishVehicleCommand
{
    public function __construct(
        public readonly int $vehicleId,
    ) {}
}
