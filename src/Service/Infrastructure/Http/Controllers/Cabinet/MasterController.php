<?php

namespace Src\Service\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\ServiceMaster;

class MasterController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $serviceProfile = $company?->serviceProfile;

        $masters = $serviceProfile ? $serviceProfile->masters()->paginate(20) : collect();

        return Inertia::render('Cabinet/Service/Masters/Index', ['masters' => $masters]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Service/Masters/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
        ]);

        $company = $request->user()->companies()->first();
        $company->serviceProfile->masters()->create($validated);

        return redirect()->route('cabinet.service.masters.index')->with('success', 'Мастер добавлен.');
    }

    public function edit(ServiceMaster $master): Response
    {
        return Inertia::render('Cabinet/Service/Masters/Edit', ['master' => $master]);
    }

    public function update(Request $request, ServiceMaster $master)
    {
        $master->update($request->only(['name', 'specialization', 'is_active']));

        return back()->with('success', 'Данные мастера обновлены.');
    }

    public function destroy(ServiceMaster $master)
    {
        $master->delete();

        return redirect()->route('cabinet.service.masters.index')->with('success', 'Мастер удалён.');
    }
}
