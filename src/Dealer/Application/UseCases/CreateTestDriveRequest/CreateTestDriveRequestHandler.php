<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CreateTestDriveRequest;

use Src\Dealer\Domain\Events\LeadCreated;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Support\Enums\LeadType;

final class CreateTestDriveRequestHandler
{
    public function handle(CreateTestDriveRequestCommand $command): DealerLead
    {
        $vehicle = Vehicle::findOrFail($command->vehicleId);

        $lead = DealerLead::create([
            'dealer_profile_id' => $vehicle->dealer_profile_id,
            'vehicle_id' => $command->vehicleId,
            'user_id' => $command->userId,
            'type' => LeadType::TEST_DRIVE,
            'name' => $command->name,
            'phone' => $command->phone,
            'email' => $command->email,
            'preferred_date' => $command->preferredDate,
            'comment' => $command->comment,
            'status' => 'new',
        ]);

        event(new LeadCreated($lead));

        return $lead;
    }
}
