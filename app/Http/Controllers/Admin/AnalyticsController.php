<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Domain\Models\Company;
use Src\Order\Domain\Models\Order;

class AnalyticsController extends Controller
{
    public function index(): Response
    {
        $ordersMonth = Order::where('created_at', '>=', now()->startOfMonth())->get();
        $revenue = $ordersMonth->sum('total_amount');

        return Inertia::render('Admin/Analytics/Index', [
            'stats' => [
                'total_users' => User::count(),
                'total_companies' => Company::count(),
                'orders_month' => $ordersMonth->count(),
                'revenue' => number_format($revenue / 100, 0, ',', ' ').' ₽',
            ],
        ]);
    }
}
