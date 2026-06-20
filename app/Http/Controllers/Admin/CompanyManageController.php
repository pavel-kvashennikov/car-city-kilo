<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Application\Services\ProfileProvisioner;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\CompanyStatus;

class CompanyManageController extends Controller
{
    public function index(Request $request): Response
    {
        $companies = Company::query()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) =>
                    $q->where('name', 'ilike', "%{$s}%")
                      ->orWhere('inn', 'ilike', "%{$s}%")
                      ->orWhere('email', 'ilike', "%{$s}%")
                )
            )
            ->with(['owner', 'businessProfiles'])
            ->withCount('businessProfiles')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
            'filters'   => $request->only(['status', 'search']),
            'counts'    => [
                'total'     => Company::count(),
                'pending'   => Company::where('status', 'pending')->count(),
                'active'    => Company::where('status', 'active')->count(),
                'suspended' => Company::where('status', 'suspended')->count(),
            ],
        ]);
    }

    public function reactivate(Company $company)
    {
        $company->update(['status' => CompanyStatus::ACTIVE, 'verified_at' => now(), 'reject_reason' => null]);
        return back()->with('success', 'Компания восстановлена.');
    }

    public function approve(Company $company)
    {
        $company->update([
            'status' => CompanyStatus::ACTIVE,
            'verified_at' => now(),
        ]);

        ProfileProvisioner::provisionFromBusinessProfiles($company->load('businessProfiles'));

        return back()->with('success', 'Компания одобрена.');
    }

    public function reject(Request $request, Company $company)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $company->update([
            'status' => CompanyStatus::REJECTED,
            'reject_reason' => $request->reason,
        ]);

        return back()->with('success', 'Компания отклонена.');
    }

    public function suspend(Company $company)
    {
        $company->update(['status' => CompanyStatus::SUSPENDED]);

        return back()->with('success', 'Компания заблокирована.');
    }
}
