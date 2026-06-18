<?php

namespace Src\Company\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Company\Application\Services\ProfileProvisioner;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\CompanyStatus;

class CompanyRegistrationController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Companies/Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inn' => ['nullable', 'string', 'max:12'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'profiles' => ['required', 'array', 'min:1'],
            'profiles.*' => ['in:dealer,parts,service'],
        ]);

        $company = Company::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'status' => CompanyStatus::PENDING,
        ]);

        $company->users()->attach($request->user()->id, ['position' => 'owner']);

        ProfileProvisioner::provisionAll($company, $validated['profiles']);

        return redirect()->route('cabinet.dashboard')
            ->with('success', 'Заявка на регистрацию отправлена. Ожидайте модерации.');
    }
}
