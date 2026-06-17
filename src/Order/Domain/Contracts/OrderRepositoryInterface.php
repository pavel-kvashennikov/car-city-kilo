<?php

declare(strict_types=1);

namespace Src\Order\Domain\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Order\Domain\Models\Order;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;

    public function findByUuid(string $uuid): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function create(array $attributes): Order;

    public function update(Order $order, array $attributes): bool;

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;
}
