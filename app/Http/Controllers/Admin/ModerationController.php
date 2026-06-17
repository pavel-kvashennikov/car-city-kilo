<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\Vehicle;

class ModerationController extends Controller
{
    public function index(): Response
    {
        $pendingVehicles = Vehicle::where('status', 'pending')
            ->with(['brand', 'carModel', 'dealerProfile.company'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Moderation/Index', [
            'pendingVehicles' => $pendingVehicles,
        ]);
    }
}
