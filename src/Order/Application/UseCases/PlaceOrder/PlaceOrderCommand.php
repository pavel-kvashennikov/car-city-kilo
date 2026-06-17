<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\PlaceOrder;

final class PlaceOrderCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly int $cartId,
        public readonly string $contactName,
        public readonly string $contactPhone,
        public readonly ?string $contactEmail = null,
        public readonly ?string $deliveryAddress = null,
        public readonly ?string $notes = null,
    ) {}
}
