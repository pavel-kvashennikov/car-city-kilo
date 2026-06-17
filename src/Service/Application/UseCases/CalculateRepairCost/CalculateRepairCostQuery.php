<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\CalculateRepairCost;

final class CalculateRepairCostQuery
{
    public function __construct(
        public readonly array $serviceItemIds,
    ) {}
}
