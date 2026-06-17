<?php

declare(strict_types=1);

namespace Src\Notification\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $notifiable->routeNotificationFor('sms') ?? $notifiable->phone;

        if (! $phone) {
            return;
        }

        Http::post('https://smsc.ru/sys/send.php', [
            'login' => config('services.smsc.login'),
            'psw' => config('services.smsc.password'),
            'phones' => $phone,
            'mes' => $message,
            'fmt' => 3,
        ]);
    }
}
