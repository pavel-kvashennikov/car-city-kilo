<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Buyer/Dashboard', [
            'recentOrders' => $user->orders()->latest()->take(5)->get(),
            'favoritesCount' => $user->favorites()->count(),
        ]);
    }
}
