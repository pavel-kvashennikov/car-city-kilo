<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\GetAvailableSlots;

final class GetAvailableSlotsQuery
{
    public function __construct(
        public readonly int $serviceProfileId,
        public readonly string $date,
        public readonly ?int $masterId = null,
    ) {}
}
