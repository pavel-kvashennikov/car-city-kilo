<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Parts\Domain\Models\Product;

class FavoriteController extends Controller
{
    private array $typeMap = [
        'vehicle' => Vehicle::class,
        'product' => Product::class,
    ];

    public function index(Request $request): Response
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['favoriteable' => function ($m) {
                $m->morphWith([
                    Vehicle::class => ['brand', 'carModel'],
                ]);
            }])
            ->latest()
            ->paginate(20);

        return Inertia::render('Buyer/Favorites', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:vehicle,product',
            'id'   => 'required|integer',
        ]);

        $morphClass = $this->typeMap[$request->type];
        $user = $request->user();

        $existing = $user->favorites()
            ->where('favoriteable_type', $morphClass)
            ->where('favoriteable_id', $request->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['is_favorited' => false]);
        }

        $user->favorites()->create([
            'favoriteable_type' => $morphClass,
            'favoriteable_id'   => $request->id,
        ]);

        return response()->json(['is_favorited' => true]);
    }

    public function destroy(Request $request, Favorite $favorite): \Illuminate\Http\RedirectResponse
    {
        // Проверяем что запись принадлежит текущему пользователю
        abort_if($favorite->user_id !== $request->user()->id, 403);

        $favorite->delete();

        return back()->with('success', 'Удалено из избранного.');
    }
}
