<?php

declare(strict_types=1);

namespace Src\Service\Domain\Contracts;

use Src\Service\Domain\Models\TimeSlot;

interface SlotBookingInterface
{
    public function bookSlot(int $timeSlotId, int $appointmentId): TimeSlot;

    public function releaseSlot(int $timeSlotId): TimeSlot;

    public function isSlotAvailable(int $timeSlotId): bool;
}
