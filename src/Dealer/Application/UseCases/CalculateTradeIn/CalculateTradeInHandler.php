<?php

declare(strict_types=1);

namespace Src\Dealer\Application\UseCases\CalculateTradeIn;

use Src\Dealer\Domain\Events\LeadCreated;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Support\Enums\LeadType;

final class CalculateTradeInHandler
{
    public function handle(CalculateTradeInCommand $command): DealerLead
    {
        $vehicle = Vehicle::findOrFail($command->vehicleId);

        $lead = DealerLead::create([
            'dealer_profile_id' => $vehicle->dealer_profile_id,
            'vehicle_id' => $command->vehicleId,
            'type' => LeadType::TRADE_IN,
            'name' => $command->name,
            'phone' => $command->phone,
            'status' => 'new',
            'meta' => [
                'trade_in_brand' => $command->tradeInBrand,
                'trade_in_model' => $command->tradeInModel,
                'trade_in_year' => $command->tradeInYear,
                'trade_in_mileage' => $command->tradeInMileage,
                'trade_in_condition' => $command->tradeInCondition,
            ],
        ]);

        event(new LeadCreated($lead));

        return $lead;
    }
}
