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
            ->withCount(['dealerProfile as has_dealer', 'partsProfile as has_parts', 'serviceProfile as has_service'])
            ->paginate(20);

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(Company $company): Response
    {
        $company->load(['dealerProfile.vehicles' => fn ($q) => $q->active()->take(6), 'partsProfile', 'serviceProfile', 'locations.zone']);

        return Inertia::render('Companies/Show', [
            'company' => $company,
        ]);
    }
}
