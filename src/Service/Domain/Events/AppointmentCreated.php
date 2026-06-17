<?php

declare(strict_types=1);

namespace Src\Service\Domain\Events;

use Src\Service\Domain\Models\Appointment;
use Src\Shared\Domain\Events\BaseEvent;

class AppointmentCreated extends BaseEvent
{
    public function __construct(
        public readonly Appointment $appointment
    ) {
        parent::__construct();
    }
}
