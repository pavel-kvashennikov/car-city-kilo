<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Billing\Domain\Models\Payment;
use Src\Billing\Domain\Models\Plan;

class BillingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Billing/Index', [
            'plans' => Plan::withCount(['subscriptions as companies_count' => fn ($q) => $q->where('status', 'active')])
                ->orderBy('sort_order')->get(),
            'recentPayments' => Payment::with('company')->latest()->take(20)->get(),
        ]);
    }
}
