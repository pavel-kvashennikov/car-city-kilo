<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CreateVehicle;

use Src\Shared\Domain\ValueObjects\Money;

final class CreateVehicleCommand
{
    public function __construct(
        public readonly int $dealerProfileId,
        public readonly string $brandId,
        public readonly string $modelId,
        public readonly int $year,
        public readonly string $vin,
        public readonly int $mileage,
        public readonly Money $price,
        public readonly array $attributes = [],
        public readonly ?string $generationId = null,
        public readonly ?string $color = null,
        public readonly ?string $engineType = null,
        public readonly ?float $engineVolume = null,
        public readonly ?int $enginePower = null,
        public readonly ?string $transmission = null,
        public readonly ?string $driveType = null,
        public readonly ?string $bodyType = null,
        public readonly ?string $condition = null,
        public readonly ?string $description = null,
    ) {}
}
