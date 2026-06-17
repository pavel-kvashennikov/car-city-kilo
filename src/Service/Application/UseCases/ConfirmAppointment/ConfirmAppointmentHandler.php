<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\ConfirmAppointment;

use Src\Service\Domain\Events\AppointmentConfirmed;
use Src\Service\Domain\Models\Appointment;

final class ConfirmAppointmentHandler
{
    public function handle(ConfirmAppointmentCommand $command): Appointment
    {
        $appointment = Appointment::findOrFail($command->appointmentId);

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        event(new AppointmentConfirmed($appointment));

        return $appointment;
    }
}
