<?php

declare(strict_types=1);

namespace Src\Parts\Application\DTO;

use Illuminate\Http\Request;

final class ProductDTO
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
        public readonly ?array $crossNumbers = null,
    ) {}

    public static function fromRequest(Request $request, int $partsProfileId): self
    {
        return new self(
            partsProfileId: $partsProfileId,
            name: $request->input('name'),
            articleNumber: $request->input('article_number'),
            oemNumber: $request->input('oem_number'),
            brand: $request->input('brand'),
            categoryId: $request->input('category_id'),
            priceRetail: $request->input('price_retail'),
            priceWholesale: $request->input('price_wholesale'),
            quantity: $request->input('quantity'),
            condition: $request->input('condition'),
            description: $request->input('description'),
            crossNumbers: $request->input('cross_numbers'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'parts_profile_id' => $this->partsProfileId,
            'name' => $this->name,
            'article_number' => $this->articleNumber,
            'oem_number' => $this->oemNumber,
            'brand' => $this->brand,
            'category_id' => $this->categoryId,
            'price_retail' => $this->priceRetail,
            'price_wholesale' => $this->priceWholesale,
            'quantity' => $this->quantity,
            'condition' => $this->condition,
            'description' => $this->description,
        ], fn ($value) => $value !== null);
    }
}
