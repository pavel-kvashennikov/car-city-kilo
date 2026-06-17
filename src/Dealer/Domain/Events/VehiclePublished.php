<?php

declare(strict_types=1);

namespace Src\Dealer\Domain\Events;

use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Domain\Events\BaseEvent;

class VehiclePublished extends BaseEvent
{
    public function __construct(
        public readonly Vehicle $vehicle
    ) {
        parent::__construct();
    }
}
