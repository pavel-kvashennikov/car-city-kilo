<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\GetAvailableSlots;

use Illuminate\Support\Collection;
use Src\Service\Domain\Models\TimeSlot;
use Src\Shared\Support\Enums\SlotStatus;

final class GetAvailableSlotsHandler
{
    public function handle(GetAvailableSlotsQuery $query): Collection
    {
        $slotsQuery = TimeSlot::where('service_profile_id', $query->serviceProfileId)
            ->where('date', $query->date)
            ->where('status', SlotStatus::AVAILABLE)
            ->with('master')
            ->orderBy('start_time');

        if ($query->masterId) {
            $slotsQuery->where('master_id', $query->masterId);
        }

        return $slotsQuery->get();
    }
}
