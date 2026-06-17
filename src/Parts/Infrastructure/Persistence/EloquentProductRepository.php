<?php

declare(strict_types=1);

namespace Src\Parts\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Parts\Domain\Contracts\ProductRepositoryInterface;
use Src\Parts\Domain\Models\Product;
use Src\Shared\Infrastructure\Persistence\BaseRepository;

class EloquentProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Product);
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findByUuid(string $uuid): ?Product
    {
        return Product::where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->first();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()->with(['partsProfile', 'crossNumbers']);

        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function update(Product $product, array $attributes): bool
    {
        return $product->update($attributes);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getByPartsProfile(int $partsProfileId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::where('parts_profile_id', $partsProfileId)
            ->with(['crossNumbers'])
            ->latest()
            ->paginate($perPage);
    }

    public function searchByOem(string $oemNumber): LengthAwarePaginator
    {
        $normalized = strtoupper(preg_replace('/[\s\-_.]/', '', $oemNumber));

        return Product::where('oem_number', 'ILIKE', "%{$normalized}%")
            ->orWhere('article_number', 'ILIKE', "%{$normalized}%")
            ->with(['partsProfile', 'crossNumbers'])
            ->paginate();
    }
}
