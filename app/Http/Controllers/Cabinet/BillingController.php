<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Billing\Domain\Models\Payment;
use Src\Billing\Domain\Models\Plan;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Order\Domain\Models\Order;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();

        return Inertia::render('Cabinet/Billing/Index', [
            'subscription' => $company?->subscription ? [
                'plan' => $company->subscription->plan,
                'ends_at' => $company->subscription->current_period_end?->format('d.m.Y'),
                'is_active' => $company->subscription->isActive(),
            ] : null,
            'payments' => $company
                ? Payment::where('company_id', $company->id)->latest()->take(10)->get()
                : collect(),
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        return back()->with('success', "Запрос на подключение тарифа «{$plan->name}» принят. Менеджер свяжется с вами для оплаты.");
    }
}
