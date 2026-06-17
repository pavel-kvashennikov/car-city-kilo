<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Order\Domain\Models\Order;

class PartsOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::with('items')
            ->latest()
            ->paginate(20);

        return Inertia::render('Cabinet/Parts/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,ready,completed,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Статус заказа обновлён.');
    }
}
