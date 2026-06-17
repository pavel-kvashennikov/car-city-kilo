<?php

declare(strict_types=1);

namespace Src\Service\Domain\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Service\Domain\Models\Appointment;

interface AppointmentRepositoryInterface
{
    public function find(int $id): ?Appointment;

    public function findByUuid(string $uuid): ?Appointment;

    public function create(array $attributes): Appointment;

    public function update(Appointment $appointment, array $attributes): bool;

    public function getByServiceProfile(int $serviceProfileId, int $perPage = 15): LengthAwarePaginator;

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;
}
