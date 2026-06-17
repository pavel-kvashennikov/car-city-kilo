<?php

declare(strict_types=1);

namespace Src\Parts\Domain\Events;

use Src\Parts\Domain\Models\Product;
use Src\Shared\Domain\Events\BaseEvent;

class StockUpdated extends BaseEvent
{
    public function __construct(
        public readonly Product $product,
        public readonly int $previousQuantity,
        public readonly int $newQuantity,
    ) {
        parent::__construct();
    }
}
