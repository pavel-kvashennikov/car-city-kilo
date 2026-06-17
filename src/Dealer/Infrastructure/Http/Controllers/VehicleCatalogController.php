<?php

namespace Src\Dealer\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Shared\Support\Enums\VehicleStatus;

class VehicleCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $vehicles = Vehicle::query()
            ->where('status', VehicleStatus::ACTIVE)
            ->when($request->brand_id, fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->model_id, fn ($q, $v) => $q->where('model_id', $v))
            ->when($request->year_from, fn ($q, $v) => $q->where('year', '>=', $v))
            ->when($request->year_to, fn ($q, $v) => $q->where('year', '<=', $v))
            ->when($request->price_from, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($request->price_to, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($request->transmission, fn ($q, $v) => $q->where('transmission', $v))
            ->when($request->engine_type, fn ($q, $v) => $q->where('engine_type', $v))
            ->when($request->condition, fn ($q, $v) => $q->where('condition', $v))
            ->with(['brand', 'carModel', 'photos' => fn ($q) => $q->where('is_main', true)])
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Catalog/Cars/Index', [
            'vehicles' => $vehicles,
            'brands' => CarBrand::orderBy('name')->get(),
            'filters' => $request->only([
                'brand_id', 'model_id', 'year_from', 'year_to',
                'price_from', 'price_to', 'transmission', 'engine_type', 'condition',
            ]),
        ]);
    }

    public function show(Vehicle $vehicle): Response
    {
        $vehicle->increment('views_count');

        $vehicle->load([
            'brand',
            'carModel',
            'generation',
            'photos',
            'dealerProfile.company',
        ]);

        $similar = Vehicle::where('brand_id', $vehicle->brand_id)
            ->where('id', '!=', $vehicle->id)
            ->where('status', VehicleStatus::ACTIVE)
            ->with(['brand', 'carModel', 'photos' => fn ($q) => $q->where('is_main', true)])
            ->take(4)
            ->get();

        return Inertia::render('Catalog/Cars/Show', [
            'vehicle' => $vehicle,
            'similar' => $similar,
        ]);
    }
}
