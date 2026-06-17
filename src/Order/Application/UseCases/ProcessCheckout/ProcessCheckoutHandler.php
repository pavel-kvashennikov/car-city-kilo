<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\ProcessCheckout;

use Src\Order\Domain\Models\Order;
use Src\Parts\Domain\Contracts\StockServiceInterface;
use Src\Service\Domain\Contracts\SlotBookingInterface;

final class ProcessCheckoutHandler
{
    public function __construct(
        private readonly StockServiceInterface $stockReservation,
        private readonly SlotBookingInterface $slotBooking,
    ) {}

    public function handle(ProcessCheckoutCommand $command): Order
    {
        $order = Order::with('items')->findOrFail($command->orderId);

        foreach ($order->items as $item) {
            if ($item->itemable_type === 'Product') {
                $this->stockReservation->reserve($item->itemable_id, $item->quantity);
            }

            if ($item->itemable_type === 'Appointment') {
                $this->slotBooking->bookSlot($item->itemable_id, $order->id);
            }
        }

        $order->update(['status' => 'processing']);

        return $order;
    }
}
