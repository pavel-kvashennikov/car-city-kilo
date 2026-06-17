<?php

declare(strict_types=1);

namespace Src\Parts\Domain\Contracts;

interface StockServiceInterface
{
    public function reserve(int $productId, int $quantity): bool;

    public function release(int $productId, int $quantity): bool;

    public function deduct(int $productId, int $quantity): bool;

    public function getAvailableQuantity(int $productId): int;
}
