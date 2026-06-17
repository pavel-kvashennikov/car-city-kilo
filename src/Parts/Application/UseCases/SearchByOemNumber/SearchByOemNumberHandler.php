<?php

declare(strict_types=1);

namespace Src\Parts\Application\UseCases\SearchByOemNumber;

use Src\Parts\Domain\Models\CrossNumber;
use Src\Parts\Domain\Models\Product;

final class SearchByOemNumberHandler
{
    public function handle(SearchByOemNumberQuery $query): array
    {
        $normalized = $this->normalize($query->oemNumber);

        $directMatches = Product::where('oem_number', 'ILIKE', "%{$normalized}%")
            ->orWhere('article_number', 'ILIKE', "%{$normalized}%")
            ->where('status', 'active')
            ->with(['partsProfile.company', 'crossNumbers'])
            ->limit($query->limit)
            ->get();

        $crossMatches = CrossNumber::where('normalized_number', 'ILIKE', "%{$normalized}%")
            ->with(['product' => fn ($q) => $q->where('status', 'active')->with('partsProfile.company')])
            ->limit($query->limit)
            ->get()
            ->pluck('product')
            ->filter()
            ->unique('id');

        return $directMatches->merge($crossMatches)->unique('id')->values()->all();
    }

    private function normalize(string $number): string
    {
        return strtoupper(preg_replace('/[\s\-_.]/', '', $number));
    }
}
