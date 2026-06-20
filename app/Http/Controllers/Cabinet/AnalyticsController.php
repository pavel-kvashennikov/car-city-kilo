<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Order\Domain\Models\Order;
use Src\Service\Domain\Models\Appointment;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $company        = $request->user()->companies()->first();
        $dealerProfile  = $company?->dealerProfile;
        $partsProfile   = $company?->partsProfile;
        $serviceProfile = $company?->serviceProfile;

        $now       = Carbon::now();
        $monthAgo  = $now->copy()->subDays(30);
        $weekAgo   = $now->copy()->subDays(7);

        // ── Dealer stats ──────────────────────────────────────────────────────
        $dealer = null;
        if ($dealerProfile) {
            $leadsTotal      = DealerLead::where('dealer_profile_id', $dealerProfile->id)->count();
            $leadsThisMonth  = DealerLead::where('dealer_profile_id', $dealerProfile->id)
                ->where('created_at', '>=', $monthAgo)->count();
            $leadsNew        = DealerLead::where('dealer_profile_id', $dealerProfile->id)
                ->where('status', 'new')->count();

            $vehiclesTotal  = $dealerProfile->vehicles()->count();
            $vehiclesActive = $dealerProfile->vehicles()->where('status', 'active')->count();

            // Top vehicles by lead count (last 30 days)
            $topVehicles = DealerLead::where('dealer_profile_id', $dealerProfile->id)
                ->where('created_at', '>=', $monthAgo)
                ->whereNotNull('vehicle_id')
                ->selectRaw('vehicle_id, count(*) as leads_count')
                ->groupBy('vehicle_id')
                ->orderByDesc('leads_count')
                ->limit(5)
                ->with('vehicle.brand', 'vehicle.carModel')
                ->get()
                ->map(fn ($l) => [
                    'title' => implode(' ', array_filter([
                        $l->vehicle?->brand?->name,
                        $l->vehicle?->carModel?->name,
                        $l->vehicle?->year,
                    ])),
                    'leads' => $l->leads_count,
                    'price' => $l->vehicle?->price,
                ]);

            // Leads by type
            $leadsByType = DealerLead::where('dealer_profile_id', $dealerProfile->id)
                ->selectRaw('lead_type, count(*) as cnt')
                ->groupBy('lead_type')
                ->get()
                ->mapWithKeys(fn ($r) => [
                    ($r->lead_type instanceof \UnitEnum ? $r->lead_type->value : (string) $r->lead_type) => $r->cnt,
                ]);

            // Daily leads last 14 days
            $dailyLeads = DealerLead::where('dealer_profile_id', $dealerProfile->id)
                ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                ->selectRaw("DATE(created_at) as date, count(*) as cnt")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->date => $r->cnt]);

            $dealer = [
                'leads_total'      => $leadsTotal,
                'leads_this_month' => $leadsThisMonth,
                'leads_new'        => $leadsNew,
                'vehicles_total'   => $vehiclesTotal,
                'vehicles_active'  => $vehiclesActive,
                'conversion'       => $vehiclesActive > 0
                    ? round($leadsTotal / $vehiclesActive, 1)
                    : 0,
                'top_vehicles'     => $topVehicles,
                'leads_by_type'    => $leadsByType,
                'daily_leads'      => $dailyLeads,
            ];
        }

        // ── Parts stats ───────────────────────────────────────────────────────
        $parts = null;
        if ($partsProfile) {
            $productsTotal  = $partsProfile->products()->count();
            $productsActive = $partsProfile->products()->where('status', 'active')->count();

            $ordersTotal = Order::whereHas(
                'items',
                fn ($q) => $q->whereHasMorph(
                    'itemable',
                    [\Src\Parts\Domain\Models\Product::class],
                    fn ($q) => $q->where('parts_profile_id', $partsProfile->id)
                )
            )->count();

            $ordersThisMonth = Order::whereHas(
                'items',
                fn ($q) => $q->whereHasMorph(
                    'itemable',
                    [\Src\Parts\Domain\Models\Product::class],
                    fn ($q) => $q->where('parts_profile_id', $partsProfile->id)
                )
            )->where('created_at', '>=', $monthAgo)->count();

            $parts = [
                'products_total'    => $productsTotal,
                'products_active'   => $productsActive,
                'orders_total'      => $ordersTotal,
                'orders_this_month' => $ordersThisMonth,
            ];
        }

        // ── Service stats ─────────────────────────────────────────────────────
        $service = null;
        if ($serviceProfile) {
            $apptTotal      = Appointment::where('service_profile_id', $serviceProfile->id)->count();
            $apptThisMonth  = Appointment::where('service_profile_id', $serviceProfile->id)
                ->where('created_at', '>=', $monthAgo)->count();
            $apptNew        = Appointment::where('service_profile_id', $serviceProfile->id)
                ->where('status', 'pending')->count();

            // Appointments by status
            $apptByStatus = Appointment::where('service_profile_id', $serviceProfile->id)
                ->selectRaw('status, count(*) as cnt')
                ->groupBy('status')
                ->get()
                ->mapWithKeys(fn ($r) => [
                    ($r->status instanceof \UnitEnum ? $r->status->value : (string) $r->status) => $r->cnt,
                ]);

            // Daily appointments last 14 days
            $dailyAppts = Appointment::where('service_profile_id', $serviceProfile->id)
                ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                ->selectRaw("DATE(created_at) as date, count(*) as cnt")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->date => $r->cnt]);

            $service = [
                'appt_total'      => $apptTotal,
                'appt_this_month' => $apptThisMonth,
                'appt_new'        => $apptNew,
                'appt_by_status'  => $apptByStatus,
                'daily_appts'     => $dailyAppts,
            ];
        }

        // ── Last 14 days labels ───────────────────────────────────────────────
        $days = collect(range(13, 0))->map(fn ($i) => $now->copy()->subDays($i)->format('Y-m-d'));

        return Inertia::render('Cabinet/Analytics/Index', [
            'dealer'  => $dealer,
            'parts'   => $parts,
            'service' => $service,
            'days'    => $days,
        ]);
    }
}
