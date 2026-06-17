<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $company = $user->companies()->first();

        return Inertia::render('Cabinet/Dashboard', [
            'company' => $company?->load(['dealerProfile', 'partsProfile', 'serviceProfile']),
        ]);
    }
}
