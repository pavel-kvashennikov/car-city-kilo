<?php

declare(strict_types=1);

namespace Src\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Src\Notification\Channels\SmsChannel;
use Src\Service\Domain\Models\Appointment;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment
    ) {
        $this->onQueue('high');
    }

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $slot = $this->appointment->timeSlot;

        return (new MailMessage)
            ->subject('Запись на сервис подтверждена')
            ->greeting("Здравствуйте, {$this->appointment->contact_name}!")
            ->line('Ваша запись на сервис подтверждена.')
            ->line("Дата: {$slot?->date}")
            ->line("Время: {$slot?->start_time} — {$slot?->end_time}")
            ->line('Ждём вас!');
    }

    public function toSms(object $notifiable): string
    {
        $slot = $this->appointment->timeSlot;

        return 'AutoMarket: Запись на сервис подтверждена. '
            ."Дата: {$slot?->date}, Время: {$slot?->start_time}";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'status' => 'confirmed',
        ];
    }
}
