<?php

declare(strict_types=1);

namespace Src\Service\Application\UseCases\CalculateRepairCost;

use Src\Service\Domain\Models\ServiceItem;

final class CalculateRepairCostHandler
{
    public function handle(CalculateRepairCostQuery $query): array
    {
        $items = ServiceItem::whereIn('id', $query->serviceItemIds)->get();

        $totalCost = $items->sum('price');
        $totalDuration = $items->sum('duration_minutes');

        return [
            'items' => $items->map(fn (ServiceItem $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'duration_minutes' => $item->duration_minutes,
            ])->toArray(),
            'total_cost' => $totalCost,
            'total_duration_minutes' => $totalDuration,
        ];
    }
}
