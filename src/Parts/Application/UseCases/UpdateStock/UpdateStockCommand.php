<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\UpdateStock;

final class UpdateStockCommand
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly string $operation = 'set',
    ) {}
}
