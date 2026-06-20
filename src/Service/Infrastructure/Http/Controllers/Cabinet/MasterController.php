<?php

namespace Src\Service\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\ServiceMaster;

class MasterController extends Controller
{
    public function index(Request $request): Response
    {
        $company        = $request->user()->companies()->first();
        $serviceProfile = $company?->serviceProfile;

        $masters = $serviceProfile
            ? $serviceProfile->masters()->orderBy('is_active', 'desc')->orderBy('name')->paginate(50)
            : collect();

        return Inertia::render('Cabinet/Service/Masters/Index', ['masters' => $masters]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Service/Masters/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'is_active'      => ['boolean'],
            'schedule'       => ['nullable', 'array'],
            'photo'          => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('masters', 'public');
        }
        unset($validated['photo']);

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
        $validated = $request->validate([
            'name'           => ['sometimes', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'is_active'      => ['boolean'],
            'schedule'       => ['nullable', 'array'],
            'photo'          => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            // Удалить старое фото
            if ($master->photo_path) {
                Storage::disk('public')->delete($master->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('masters', 'public');
        }
        unset($validated['photo']);

        $master->update($validated);

        return back()->with('success', 'Данные мастера обновлены.');
    }

    public function destroy(ServiceMaster $master)
    {
        if ($master->photo_path) {
            Storage::disk('public')->delete($master->photo_path);
        }
        $master->delete();

        return redirect()->route('cabinet.service.masters.index')->with('success', 'Мастер удалён.');
    }
}
