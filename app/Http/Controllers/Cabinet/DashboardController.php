<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Order\Domain\Models\Order;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $company = $user->companies()->with(['businessProfiles', 'dealerProfile', 'partsProfile', 'serviceProfile'])->first();

        $stats = [
            'views_today' => 0,
            'leads_week' => 0,
            'orders_month' => 0,
            'impressions_growth' => 0,
        ];

        if ($company?->dealerProfile) {
            $stats['leads_week'] = DealerLead::where('dealer_profile_id', $company->dealerProfile->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()])
                ->count();
        }

        if ($company) {
            try {
                $stats['orders_month'] = Order::where('company_id', $company->id)
                    ->whereMonth('created_at', now()->month)
                    ->count();
            } catch (\Throwable) {
            }
        }

        if ($company) {
            $company->setAttribute(
                'active_profiles',
                $company->businessProfiles->map(fn ($p) => [
                    'type' => $p->type?->value ?? (string) $p->type,
                    'id' => $p->id,
                ])->toArray()
            );
        }

        return Inertia::render('Cabinet/Dashboard', [
            'company' => $company,
            'stats' => $stats,
        ]);
    }
}
