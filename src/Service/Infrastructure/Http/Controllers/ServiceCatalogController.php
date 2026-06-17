<?php

namespace Src\Service\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\ServiceProfile;

class ServiceCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $services = ServiceProfile::query()
            ->with('company')
            ->when($request->search, fn ($q, $s) => $q->whereHas('company', fn ($c) => $c->where('name', 'ilike', "%{$s}%")))
            ->paginate(20);

        return Inertia::render('Catalog/Services/Index', [
            'services' => $services,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(ServiceProfile $serviceProfile): Response
    {
        $serviceProfile->load(['company', 'serviceItems', 'masters']);

        return Inertia::render('Catalog/Services/Show', [
            'service' => $serviceProfile,
        ]);
    }
}
