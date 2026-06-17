<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\AddToCart;

final class AddToCartCommand
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?string $sessionId,
        public readonly int $itemableId,
        public readonly string $itemableType,
        public readonly int $quantity = 1,
        public readonly int $unitPrice = 0,
        public readonly array $snapshot = [],
        public readonly array $meta = [],
    ) {}
}
