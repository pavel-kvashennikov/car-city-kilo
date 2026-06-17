<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        $favorites = $request->user()
            ->favorites()
            ->with('favoriteable')
            ->latest()
            ->paginate(20);

        return Inertia::render('Buyer/Favorites', [
            'favorites' => $favorites,
        ]);
    }
}
