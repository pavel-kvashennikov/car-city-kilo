<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\UpdateStock;

use Src\Parts\Domain\Events\StockUpdated;
use Src\Parts\Domain\Models\Product;

final class UpdateStockHandler
{
    public function handle(UpdateStockCommand $command): Product
    {
        $product = Product::findOrFail($command->productId);

        $previousQuantity = $product->quantity;

        $newQuantity = match ($command->operation) {
            'set' => $command->quantity,
            'add' => $previousQuantity + $command->quantity,
            'subtract' => max(0, $previousQuantity - $command->quantity),
            default => $command->quantity,
        };

        $product->update(['quantity' => $newQuantity]);

        event(new StockUpdated($product, $previousQuantity, $newQuantity));

        return $product;
    }
}
