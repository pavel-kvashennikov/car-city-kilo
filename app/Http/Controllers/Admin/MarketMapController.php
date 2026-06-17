<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\MarketMap\Domain\Models\MarketZone;

class MarketMapController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/MarketMap/Editor', [
            'zones' => MarketZone::with('locations.company')->get(),
        ]);
    }
}
