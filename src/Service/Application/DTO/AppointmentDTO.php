<?php

declare(strict_types=1);

namespace Src\Service\Application\DTO;

use Illuminate\Http\Request;

final class AppointmentDTO
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

    public static function fromRequest(Request $request): self
    {
        return new self(
            serviceProfileId: $request->input('service_profile_id'),
            timeSlotId: $request->input('time_slot_id'),
            contactName: $request->input('contact_name'),
            contactPhone: $request->input('contact_phone'),
            masterId: $request->input('master_id'),
            userId: $request->user()?->id,
            carBrand: $request->input('car_brand'),
            carModel: $request->input('car_model'),
            carYear: $request->input('car_year'),
            carVin: $request->input('car_vin'),
            carMileage: $request->input('car_mileage'),
            serviceItems: $request->input('service_items'),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'service_profile_id' => $this->serviceProfileId,
            'time_slot_id' => $this->timeSlotId,
            'contact_name' => $this->contactName,
            'contact_phone' => $this->contactPhone,
            'master_id' => $this->masterId,
            'user_id' => $this->userId,
            'car_brand' => $this->carBrand,
            'car_model' => $this->carModel,
            'car_year' => $this->carYear,
            'car_vin' => $this->carVin,
            'car_mileage' => $this->carMileage,
            'service_items' => $this->serviceItems,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }
}
