<?php

namespace Src\Service\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\ServiceCategory;
use Src\Service\Domain\Models\ServiceItem;

class ServiceItemController extends Controller
{
    public function index(Request $request): Response
    {
        $company        = $request->user()->companies()->first();
        $serviceProfile = $company?->serviceProfile;

        $items = $serviceProfile
            ? $serviceProfile->serviceItems()
                ->with('category')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(50)
            : collect();

        return Inertia::render('Cabinet/Service/ServiceItems/Index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Service/ServiceItems/Create', [
            'categories' => ServiceCategory::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'category_id'      => ['nullable', 'exists:service_categories,id'],
            'description'      => ['nullable', 'string'],
            'price_fixed'      => ['nullable', 'integer', 'min:0'],
            'price_from'       => ['nullable', 'integer', 'min:0'],
            'price_to'         => ['nullable', 'integer', 'min:0'],
            'price_per_hour'   => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'vehicle_types'    => ['nullable', 'array'],
            'vehicle_types.*'  => ['string'],
            'is_active'        => ['boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $company = $request->user()->companies()->first();
        $company->serviceProfile->serviceItems()->create($validated);

        return redirect()->route('cabinet.service.items.index')->with('success', 'Услуга добавлена.');
    }

    public function edit(ServiceItem $item): Response
    {
        return Inertia::render('Cabinet/Service/ServiceItems/Edit', [
            'item'       => $item,
            'categories' => ServiceCategory::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, ServiceItem $item)
    {
        $validated = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'category_id'      => ['nullable', 'exists:service_categories,id'],
            'description'      => ['nullable', 'string'],
            'price_fixed'      => ['nullable', 'integer', 'min:0'],
            'price_from'       => ['nullable', 'integer', 'min:0'],
            'price_to'         => ['nullable', 'integer', 'min:0'],
            'price_per_hour'   => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'vehicle_types'    => ['nullable', 'array'],
            'vehicle_types.*'  => ['string'],
            'is_active'        => ['boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $item->update($validated);

        return back()->with('success', 'Услуга обновлена.');
    }

    public function destroy(ServiceItem $item)
    {
        $item->delete();

        return redirect()->route('cabinet.service.items.index')->with('success', 'Услуга удалена.');
    }
}
