<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Order\Domain\Models\Order;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $dealerProfile = $company?->dealerProfile;

        $leadsCount = $dealerProfile
            ? DealerLead::where('dealer_profile_id', $dealerProfile->id)->count()
            : 0;

        $ordersCount = $company
            ? Order::whereHas('items', fn ($q) => $q->whereHasMorph(
                'itemable',
                [\Src\Parts\Domain\Models\Product::class],
                fn ($q) => $q->where('parts_profile_id', $company->partsProfile?->id)
            ))->count()
            : 0;

        $views = $dealerProfile?->vehicles()->count() ?? 0;
        $conversion = $views > 0 ? round(($leadsCount / $views) * 100, 1).'%' : '0%';

        return Inertia::render('Cabinet/Analytics/Index', [
            'stats' => [
                'views' => $views,
                'leads' => $leadsCount,
                'orders' => $ordersCount,
                'conversion' => $conversion,
            ],
        ]);
    }
}
