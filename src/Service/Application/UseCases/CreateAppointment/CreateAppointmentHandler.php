<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\CreateAppointment;

use Illuminate\Support\Facades\DB;
use Src\Service\Domain\Events\AppointmentCreated;
use Src\Service\Domain\Models\Appointment;
use Src\Service\Domain\Models\TimeSlot;
use Src\Shared\Support\Enums\SlotStatus;

final class CreateAppointmentHandler
{
    public function handle(CreateAppointmentCommand $command): Appointment
    {
        return DB::transaction(function () use ($command) {
            $slot = TimeSlot::where('id', $command->timeSlotId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($slot->status !== SlotStatus::AVAILABLE) {
                throw new \DomainException('Слот уже занят. Пожалуйста, выберите другое время.');
            }

            $estimatedCost = $this->calculateEstimatedCost($command->serviceItems ?? []);

            $appointment = Appointment::create([
                'service_profile_id' => $command->serviceProfileId,
                'time_slot_id' => $command->timeSlotId,
                'master_id' => $command->masterId ?? $slot->master_id,
                'user_id' => $command->userId,
                'contact_name' => $command->contactName,
                'contact_phone' => $command->contactPhone,
                'car_brand' => $command->carBrand,
                'car_model' => $command->carModel,
                'car_year' => $command->carYear,
                'car_vin' => $command->carVin,
                'car_mileage' => $command->carMileage,
                'service_items' => $command->serviceItems,
                'estimated_cost' => $estimatedCost,
                'notes' => $command->notes,
                'status' => 'pending',
            ]);

            $slot->update([
                'status' => SlotStatus::BOOKED,
                'appointment_id' => $appointment->id,
            ]);

            event(new AppointmentCreated($appointment));

            return $appointment;
        });
    }

    private function calculateEstimatedCost(?array $serviceItems): int
    {
        if (empty($serviceItems)) {
            return 0;
        }

        return collect($serviceItems)->sum(fn ($item) => $item['price'] ?? 0);
    }
}
