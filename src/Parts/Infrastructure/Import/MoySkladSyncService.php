<?php

declare(strict_types=1);

namespace Src\Parts\Infrastructure\Import;

use Illuminate\Support\Facades\Http;
use Src\Parts\Domain\Models\PartsProfile;
use Src\Parts\Domain\Models\Product;

class MoySkladSyncService
{
    public function syncProducts(PartsProfile $profile): array
    {
        $token = $profile->moysklad_token ?? null;

        if (! $token) {
            return ['synced' => 0, 'error' => 'МойСклад token not configured'];
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->get('https://api.moysklad.ru/api/remap/1.2/entity/product');

        if (! $response->successful()) {
            return ['synced' => 0, 'error' => 'API request failed: '.$response->status()];
        }

        $products = $response->json('rows', []);
        $synced = 0;

        foreach ($products as $msSku) {
            Product::updateOrCreate(
                [
                    'parts_profile_id' => $profile->id,
                    'article_number' => $msSku['article'] ?? $msSku['code'] ?? null,
                ],
                [
                    'name' => $msSku['name'],
                    'description' => $msSku['description'] ?? null,
                    'price_retail' => isset($msSku['salePrices'][0]['value'])
                        ? (int) ($msSku['salePrices'][0]['value'] / 100)
                        : null,
                    'quantity' => $msSku['quantity'] ?? 0,
                    'status' => 'active',
                ]
            );

            $synced++;
        }

        return ['synced' => $synced, 'error' => null];
    }
}
