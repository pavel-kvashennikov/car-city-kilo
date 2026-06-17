<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Domain\Models\Company;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Order\Domain\Models\Order;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_companies' => Company::count(),
                'pending_companies' => Company::where('status', 'pending')->count(),
                'active_vehicles' => Vehicle::where('status', 'active')->count(),
                'recent_orders' => Order::where('created_at', '>=', now()->subDays(30))->count(),
            ],
        ]);
    }
}
