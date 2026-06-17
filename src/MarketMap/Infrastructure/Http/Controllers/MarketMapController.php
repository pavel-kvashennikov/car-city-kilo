<?php

namespace Src\MarketMap\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\MarketMap\Domain\Models\MarketZone;

class MarketMapController extends Controller
{
    public function index(): Response
    {
        $zones = MarketZone::with(['locations.company'])->orderBy('sort_order')->get();

        return Inertia::render('MarketMap/Index', [
            'zones' => $zones,
        ]);
    }
}
