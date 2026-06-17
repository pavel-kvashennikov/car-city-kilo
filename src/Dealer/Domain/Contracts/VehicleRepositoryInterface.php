<?php

declare(strict_types=1);

namespace Src\Dealer\Domain\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Dealer\Domain\Models\Vehicle;

interface VehicleRepositoryInterface
{
    public function find(int $id): ?Vehicle;

    public function findByUuid(string $uuid): ?Vehicle;

    public function findBySlug(string $slug): ?Vehicle;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Vehicle;

    public function update(Vehicle $vehicle, array $attributes): bool;

    public function delete(Vehicle $vehicle): bool;

    public function getByDealerProfile(int $dealerProfileId, int $perPage = 15): LengthAwarePaginator;

    public function getActiveVehicles(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
