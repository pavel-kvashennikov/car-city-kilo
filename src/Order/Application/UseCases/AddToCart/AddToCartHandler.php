<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\AddToCart;

use Src\Order\Domain\Models\Cart;
use Src\Order\Domain\Models\CartItem;

final class AddToCartHandler
{
    public function handle(AddToCartCommand $command): CartItem
    {
        $cart = Cart::firstOrCreate(
            array_filter([
                'user_id' => $command->userId,
                'session_id' => $command->sessionId,
            ]),
            ['expires_at' => now()->addDays(7)]
        );

        $existingItem = $cart->items()
            ->where('itemable_id', $command->itemableId)
            ->where('itemable_type', $command->itemableType)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $command->quantity,
            ]);

            return $existingItem;
        }

        return $cart->items()->create([
            'itemable_id' => $command->itemableId,
            'itemable_type' => $command->itemableType,
            'quantity' => $command->quantity,
            'unit_price' => $command->unitPrice,
            'snapshot' => $command->snapshot,
            'meta' => $command->meta,
        ]);
    }
}
