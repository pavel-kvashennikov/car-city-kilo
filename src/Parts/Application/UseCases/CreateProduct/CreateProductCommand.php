<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\CreateProduct;

final class CreateProductCommand
{
    public function __construct(
        public readonly int $partsProfileId,
        public readonly string $name,
        public readonly ?string $articleNumber = null,
        public readonly ?string $oemNumber = null,
        public readonly ?string $brand = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $priceRetail = null,
        public readonly ?int $priceWholesale = null,
        public readonly ?int $quantity = null,
        public readonly ?string $condition = null,
        public readonly ?string $description = null,
        public readonly array $crossNumbers = [],
    ) {}
}
