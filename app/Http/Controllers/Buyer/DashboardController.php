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
            'ordersCount' => $user->orders()->count(),
            'appointmentsCount' => \Src\Service\Domain\Models\Appointment::where('user_id', $user->id)->count(),
            'favoritesCount' => $user->favorites()->count(),
        ]);
    }
}
