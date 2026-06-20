<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Order\Domain\Models\Order;
use Src\Parts\Domain\Models\Product;

class PartsOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $company      = $request->user()->companies()->first();
        $partsProfile = $company?->partsProfile;

        if (! $partsProfile) {
            return Inertia::render('Cabinet/Parts/Orders/Index', [
                'orders'  => ['data' => [], 'links' => []],
                'filters' => [],
            ]);
        }

        $orders = Order::with([
                'items',
                'user',
            ])
            ->whereHas('items', fn ($q) => $q->whereHasMorph(
                'itemable',
                [Product::class],
                fn ($q) => $q->where('parts_profile_id', $partsProfile->id)
            ))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->through(function (Order $order) use ($partsProfile) {
                // Оставляем только позиции, относящиеся к этому профилю
                $order->setRelation(
                    'items',
                    $order->items->filter(fn ($item) =>
                        $item->itemable_type === Product::class &&
                        optional(Product::find($item->itemable_id))->parts_profile_id === $partsProfile->id
                    )->values()
                );
                return $order;
            });

        return Inertia::render('Cabinet/Parts/Orders/Index', [
            'orders'  => $orders,
            'filters' => $request->only('status'),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        // Проверить, что заказ содержит товары этого профиля
        $company      = $request->user()->companies()->first();
        $partsProfile = $company?->partsProfile;

        abort_unless(
            $partsProfile && $order->items()->whereHasMorph(
                'itemable',
                [Product::class],
                fn ($q) => $q->where('parts_profile_id', $partsProfile->id)
            )->exists(),
            403
        );

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,ready,completed,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Статус заказа обновлён.');
    }
}
