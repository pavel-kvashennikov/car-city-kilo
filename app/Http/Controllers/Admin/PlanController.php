<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Billing\Domain\Models\Plan;

class PlanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Billing/Plans', [
            'plans' => Plan::orderBy('sort_order')->get(),
        ]);
    }
}
