<?php

namespace Src\Dealer\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Dealer\Domain\Models\Vehicle;

class VehicleController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $dealerProfile = $company?->dealerProfile;

        $vehicles = $dealerProfile
            ? $dealerProfile->vehicles()
                ->with(['brand', 'carModel', 'photos' => fn ($q) => $q->where('is_main', true)])
                ->latest()
                ->paginate(20)
            : collect();

        return Inertia::render('Cabinet/Dealer/Vehicles/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Dealer/Vehicles/Create', [
            'brands' => CarBrand::with('models.generations')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => ['required', 'exists:car_brands,id'],
            'model_id' => ['required', 'exists:car_models,id'],
            'generation_id' => ['nullable', 'exists:car_generations,id'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'vin' => ['nullable', 'string', 'size:17'],
            'mileage' => ['required', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'engine_type' => ['nullable', 'string', 'in:petrol,diesel,hybrid,electric,gas'],
            'engine_volume' => ['nullable', 'numeric', 'min:0'],
            'engine_power' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'in:manual,automatic,robot,cvt'],
            'drive_type' => ['nullable', 'string', 'in:fwd,rwd,awd'],
            'body_type' => ['nullable', 'string', 'max:30'],
            'condition' => ['required', 'string', 'in:new,used,damaged'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
        ]);

        $company = $request->user()->companies()->first();
        $dealerProfile = $company->dealerProfile;

        $vehicle = $dealerProfile->vehicles()->create([
            ...$validated,
            'status' => 'draft',
        ]);

        return redirect()->route('cabinet.dealer.vehicles.edit', $vehicle)
            ->with('success', 'Автомобиль создан. Добавьте фотографии и опубликуйте.');
    }

    public function show(Vehicle $vehicle): Response
    {
        $vehicle->load(['brand', 'carModel', 'generation', 'photos', 'leads']);

        return Inertia::render('Cabinet/Dealer/Vehicles/Show', [
            'vehicle' => $vehicle,
        ]);
    }

    public function edit(Vehicle $vehicle): Response
    {
        $vehicle->load('photos');

        return Inertia::render('Cabinet/Dealer/Vehicles/Edit', [
            'vehicle' => $vehicle,
            'brands' => CarBrand::with('models.generations')->get(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'brand_id' => ['sometimes', 'exists:car_brands,id'],
            'model_id' => ['sometimes', 'exists:car_models,id'],
            'year' => ['sometimes', 'integer', 'min:1900'],
            'mileage' => ['sometimes', 'integer', 'min:0'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:draft,pending,active,sold,archived'],
        ]);

        $vehicle->update($validated);

        return back()->with('success', 'Автомобиль обновлён.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('cabinet.dealer.vehicles.index')
            ->with('success', 'Автомобиль удалён.');
    }
}
