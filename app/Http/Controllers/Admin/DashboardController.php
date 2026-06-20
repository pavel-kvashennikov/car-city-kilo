<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Domain\Models\Company;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Order\Domain\Models\Order;
use Src\Parts\Domain\Models\Product;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Service\Domain\Models\Appointment;
use App\Models\BlogPost;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = now()->startOfDay();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'companies'         => Company::count(),
                'companies_active'  => Company::where('status', 'active')->count(),
                'pending'           => Company::where('status', 'pending')->count(),
                'users'             => User::count(),
                'vehicles'          => Vehicle::where('status', 'active')->count(),
                'products'          => Product::where('status', 'active')->count(),
                'services'          => ServiceProfile::count(),
                'orders_today'      => Order::where('created_at', '>=', $today)->count(),
                'orders_month'      => Order::where('created_at', '>=', now()->startOfMonth())->count(),
                'appointments_month'=> Appointment::where('created_at', '>=', now()->startOfMonth())->count(),
                'blog_posts'        => BlogPost::where('status', 'published')->count(),
                'revenue_month'     => (int) Order::where('created_at', '>=', now()->startOfMonth())
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount'),
            ],
            'recentCompanies' => Company::with('owner')
                ->latest()
                ->limit(5)
                ->get(['id', 'name', 'status', 'created_at', 'owner_id']),
            'recentOrders' => Order::with('user')
                ->latest()
                ->limit(5)
                ->get(['id', 'order_number', 'status', 'total_amount', 'created_at', 'user_id']),
        ]);
    }
}
