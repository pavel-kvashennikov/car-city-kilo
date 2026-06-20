<?php

namespace Src\Company\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\CompanyStatus;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $companies = Company::query()
            ->where('status', CompanyStatus::ACTIVE)
            ->when($request->search, fn ($q, $s) => $q->where('name', 'ilike', "%{$s}%"))
            ->with(['businessProfiles'])
            ->paginate(20);

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(Company $company): Response
    {
        $company->load(['businessProfiles']);

        $vehicles = [];
        $products = [];
        $services = [];

        if ($company->dealerProfile) {
            $vehicles = $company->dealerProfile
                ->vehicles()
                ->where('status', 'active')
                ->with(['brand', 'carModel', 'photos' => fn ($q) => $q->where('is_main', true)])
                ->take(8)
                ->get();
        }

        if ($company->partsProfile) {
            $products = $company->partsProfile
                ->products()
                ->where('status', 'active')
                ->with(['category'])
                ->take(10)
                ->get();
        }

        if ($company->serviceProfile) {
            $services = [$company->serviceProfile->load(['serviceItems' => fn ($q) => $q->take(5)])];
        }

        return Inertia::render('Companies/Show', [
            'company' => $company,
            'vehicles' => $vehicles,
            'products' => $products,
            'services' => $services,
        ]);
    }
}
