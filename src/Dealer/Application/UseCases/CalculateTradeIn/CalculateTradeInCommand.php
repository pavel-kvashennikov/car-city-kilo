<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CalculateTradeIn;

final class CalculateTradeInCommand
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $tradeInBrand,
        public readonly string $tradeInModel,
        public readonly int $tradeInYear,
        public readonly int $tradeInMileage,
        public readonly ?string $tradeInCondition = null,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
    ) {}
}
