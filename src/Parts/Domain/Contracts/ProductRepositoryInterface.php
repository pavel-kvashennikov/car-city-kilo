<?php

declare(strict_types=1);

namespace Src\Parts\Domain\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Parts\Domain\Models\Product;

interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;

    public function findByUuid(string $uuid): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): bool;

    public function delete(Product $product): bool;

    public function getByPartsProfile(int $partsProfileId, int $perPage = 15): LengthAwarePaginator;

    public function searchByOem(string $oemNumber): LengthAwarePaginator;
}
