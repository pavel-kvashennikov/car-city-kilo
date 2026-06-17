<?php

declare(strict_types=1);

namespace Src\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Src\Notification\Channels\SmsChannel;
use Src\Order\Domain\Models\Order;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {
        $this->onQueue('high');
    }

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Заказ {$this->order->order_number} оформлен")
            ->greeting("Здравствуйте, {$notifiable->name}!")
            ->line("Ваш заказ {$this->order->order_number} успешно оформлен.")
            ->line('Сумма: '.number_format($this->order->total_amount / 100, 0, '.', ' ').' ₽')
            ->action('Посмотреть заказ', url("/buyer/orders/{$this->order->uuid}"))
            ->line('Спасибо за покупку!');
    }

    public function toSms(object $notifiable): string
    {
        return "AutoMarket: Заказ {$this->order->order_number} оформлен. "
            .'Сумма: '.number_format($this->order->total_amount / 100, 0, '.', ' ').' ₽';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
        ];
    }
}
