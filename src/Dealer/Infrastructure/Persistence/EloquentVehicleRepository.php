<?php

declare(strict_types=1);

namespace Src\Dealer\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Dealer\Domain\Contracts\VehicleRepositoryInterface;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Infrastructure\Persistence\BaseRepository;
use Src\Shared\Support\Enums\VehicleStatus;

class EloquentVehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Vehicle);
    }

    public function find(int $id): ?Vehicle
    {
        return Vehicle::find($id);
    }

    public function findByUuid(string $uuid): ?Vehicle
    {
        return Vehicle::where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Vehicle
    {
        return Vehicle::where('slug', $slug)->first();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Vehicle::query()->with(['brand', 'carModel', 'photos']);

        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    public function create(array $attributes): Vehicle
    {
        return Vehicle::create($attributes);
    }

    public function update(Vehicle $vehicle, array $attributes): bool
    {
        return $vehicle->update($attributes);
    }

    public function delete(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    public function getByDealerProfile(int $dealerProfileId, int $perPage = 15): LengthAwarePaginator
    {
        return Vehicle::where('dealer_profile_id', $dealerProfileId)
            ->with(['brand', 'carModel', 'photos'])
            ->latest()
            ->paginate($perPage);
    }

    public function getActiveVehicles(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Vehicle::where('status', VehicleStatus::ACTIVE)
            ->with(['brand', 'carModel', 'photos', 'dealerProfile.company']);

        $this->applyFilters($query, $filters);

        return $query->latest('published_at')->paginate($perPage);
    }
}
