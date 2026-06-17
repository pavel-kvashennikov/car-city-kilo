<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\CreateAppointment;

final class CreateAppointmentCommand
{
    public function __construct(
        public readonly int $serviceProfileId,
        public readonly int $timeSlotId,
        public readonly string $contactName,
        public readonly string $contactPhone,
        public readonly ?int $masterId = null,
        public readonly ?int $userId = null,
        public readonly ?string $carBrand = null,
        public readonly ?string $carModel = null,
        public readonly ?int $carYear = null,
        public readonly ?string $carVin = null,
        public readonly ?int $carMileage = null,
        public readonly ?array $serviceItems = null,
        public readonly ?string $notes = null,
    ) {}
}
