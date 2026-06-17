<?php

declare(strict_types=1);

namespace Src\Parts\Infrastructure\Search;

use Meilisearch\Client;
use Src\Parts\Domain\Models\Product;

class ProductSearchIndexer
{
    public function __construct(
        private readonly Client $meilisearch,
    ) {}

    public function index(Product $product): void
    {
        $this->meilisearch->index('products')
            ->addDocuments([$this->toDocument($product)]);
    }

    public function delete(Product $product): void
    {
        $this->meilisearch->index('products')
            ->deleteDocument($product->id);
    }

    public function bulkIndex(iterable $products): void
    {
        $documents = [];
        foreach ($products as $product) {
            $documents[] = $this->toDocument($product);
        }

        if (! empty($documents)) {
            $this->meilisearch->index('products')
                ->addDocuments($documents);
        }
    }

    private function toDocument(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'article_number' => $product->article_number,
            'oem_number' => $product->oem_number,
            'cross_numbers' => $product->crossNumbers->pluck('normalized_number')->toArray(),
            'brand' => $product->brand,
            'category_id' => $product->category_id,
            'price_retail' => $product->price_retail,
            'in_stock' => ($product->quantity ?? 0) > 0,
            'quantity' => $product->quantity ?? 0,
            'condition' => $product->condition,
            'status' => $product->status,
            'parts_profile_id' => $product->parts_profile_id,
            'store_name' => $product->partsProfile?->company?->name,
            'description' => $product->description,
        ];
    }
}
