<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\PlaceOrder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Src\Order\Domain\Events\OrderPlaced;
use Src\Order\Domain\Models\Cart;
use Src\Order\Domain\Models\Order;

final class PlaceOrderHandler
{
    public function handle(PlaceOrderCommand $command): Order
    {
        return DB::transaction(function () use ($command) {
            $cart = Cart::with('items')->findOrFail($command->cartId);

            $totalAmount = $cart->items->sum(fn ($item) => $item->unit_price * $item->quantity);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $command->userId,
                'total_amount' => $totalAmount,
                'contact_name' => $command->contactName,
                'contact_phone' => $command->contactPhone,
                'contact_email' => $command->contactEmail,
                'delivery_address' => $command->deliveryAddress,
                'notes' => $command->notes,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'itemable_id' => $cartItem->itemable_id,
                    'itemable_type' => $cartItem->itemable_type,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->unit_price * $cartItem->quantity,
                    'snapshot' => $cartItem->snapshot,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            event(new OrderPlaced($order));

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        return 'AM-'.date('Ymd').'-'.strtoupper(Str::random(6));
    }
}
