<?php

declare(strict_types=1);

namespace Src\Service\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Service\Domain\Contracts\AppointmentRepositoryInterface;
use Src\Service\Domain\Models\Appointment;
use Src\Shared\Infrastructure\Persistence\BaseRepository;

class EloquentAppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Appointment);
    }

    public function find(int $id): ?Appointment
    {
        return Appointment::find($id);
    }

    public function findByUuid(string $uuid): ?Appointment
    {
        return Appointment::where('uuid', $uuid)->first();
    }

    public function create(array $attributes): Appointment
    {
        return Appointment::create($attributes);
    }

    public function update(Appointment $appointment, array $attributes): bool
    {
        return $appointment->update($attributes);
    }

    public function getByServiceProfile(int $serviceProfileId, int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::where('service_profile_id', $serviceProfileId)
            ->with(['timeSlot', 'master'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::where('user_id', $userId)
            ->with(['serviceProfile.company', 'timeSlot', 'master'])
            ->latest()
            ->paginate($perPage);
    }
}
