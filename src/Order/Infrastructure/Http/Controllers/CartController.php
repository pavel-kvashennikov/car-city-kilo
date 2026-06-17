<?php

namespace Src\Order\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Src\Order\Domain\Models\Cart;
use Src\Order\Domain\Models\CartItem;
use Src\Order\Domain\Models\Order;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        $cart = $this->getCart($request);
        $cart?->load('items.itemable');

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'itemable_type' => ['required', 'string'],
            'itemable_id' => ['required', 'integer'],
            'quantity' => ['integer', 'min:1', 'max:99'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $cart = $this->getOrCreateCart($request);

        $existing = $cart->items()
            ->where('itemable_type', $validated['itemable_type'])
            ->where('itemable_id', $validated['itemable_id'])
            ->first();

        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + ($validated['quantity'] ?? 1)]);
        } else {
            $cart->items()->create([
                ...$validated,
                'quantity' => $validated['quantity'] ?? 1,
            ]);
        }

        return back()->with('success', 'Товар добавлен в корзину.');
    }

    public function removeItem(CartItem $cartItem)
    {
        $cartItem->delete();

        return back()->with('success', 'Товар удалён из корзины.');
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'client_email' => ['nullable', 'email'],
            'delivery_method' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:delivery_method,delivery', 'nullable', 'array'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = $this->getCart($request);

        if (! $cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Корзина пуста.']);
        }

        $cart->load('items.itemable');

        $order = Order::create([
            'order_number' => $this->generateOrderNumber(),
            'user_id' => $request->user()->id,
            'total_amount' => $cart->items->sum(fn ($i) => $i->price * $i->quantity),
            ...$validated,
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'itemable_type' => $item->itemable_type,
                'itemable_id' => $item->itemable_id,
                'name' => $item->itemable?->name ?? 'Товар',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->price * $item->quantity,
            ]);
        }

        $cart->items()->delete();
        $cart->delete();

        return redirect()->route('buyer.orders.index')
            ->with('success', "Заказ #{$order->order_number} оформлен!");
    }

    private function getCart(Request $request): ?Cart
    {
        return Cart::where('user_id', $request->user()->id)->first();
    }

    private function getOrCreateCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function generateOrderNumber(): string
    {
        return 'AM-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
    }
}
