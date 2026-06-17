<?php

namespace Src\Company\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanySettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        $company = $request->user()->companies()->first();

        return Inertia::render('Cabinet/Company/Edit', [
            'company' => $company,
        ]);
    }

    public function update(Request $request)
    {
        $company = $request->user()->companies()->first();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'email'],
            'working_hours' => ['nullable', 'array'],
        ]);

        $company->update($validated);

        return back()->with('success', 'Данные компании обновлены.');
    }
}
