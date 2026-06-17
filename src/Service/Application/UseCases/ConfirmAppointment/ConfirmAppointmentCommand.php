<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\ConfirmAppointment;

final class ConfirmAppointmentCommand
{
    public function __construct(
        public readonly int $appointmentId,
    ) {}
}
