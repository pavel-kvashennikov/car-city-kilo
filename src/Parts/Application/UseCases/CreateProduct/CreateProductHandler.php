<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\CreateProduct;

use Illuminate\Support\Facades\DB;
use Src\Parts\Domain\Contracts\ProductRepositoryInterface;
use Src\Parts\Domain\Events\ProductCreated;
use Src\Parts\Domain\Models\CrossNumber;
use Src\Parts\Domain\Models\Product;

final class CreateProductHandler
{
    public function __construct(
        private readonly ProductRepositoryInterface $products
    ) {}

    public function handle(CreateProductCommand $command): Product
    {
        return DB::transaction(function () use ($command) {
            $product = $this->products->create([
                'parts_profile_id' => $command->partsProfileId,
                'name' => $command->name,
                'article_number' => $command->articleNumber,
                'oem_number' => $command->oemNumber,
                'brand' => $command->brand,
                'category_id' => $command->categoryId,
                'price_retail' => $command->priceRetail,
                'price_wholesale' => $command->priceWholesale,
                'quantity' => $command->quantity ?? 0,
                'condition' => $command->condition ?? 'new',
                'description' => $command->description,
                'status' => 'active',
            ]);

            foreach ($command->crossNumbers as $crossNumber) {
                CrossNumber::create([
                    'product_id' => $product->id,
                    'number' => $crossNumber,
                    'normalized_number' => strtoupper(preg_replace('/[\s\-_.]/', '', $crossNumber)),
                ]);
            }

            event(new ProductCreated($product));

            return $product;
        });
    }
}
