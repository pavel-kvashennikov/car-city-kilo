<?php

declare(strict_types=1);

namespace Src\Notification\Channels;

use Illuminate\Notifications\Notification;

class PushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toPush')) {
            return;
        }

        $data = $notification->toPush($notifiable);

        // Push notification implementation via Firebase/WebPush
        // To be implemented when push notifications are set up
    }
}
