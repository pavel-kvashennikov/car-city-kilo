<?php

namespace Src\Service\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Service\Domain\Models\TimeSlot;
use Src\Shared\Support\Enums\SlotStatus;

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

        // Доступные слоты на ближайшие 14 дней, сгруппированные по датам
        $slots = TimeSlot::where('service_profile_id', $serviceProfile->id)
            ->where('status', SlotStatus::AVAILABLE)
            ->where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(14))
            ->with('master:id,name')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get(['id', 'date', 'start_time', 'end_time', 'master_id', 'status'])
            ->groupBy(fn ($s) => Carbon::parse($s->date)->toDateString())
            ->map(fn ($group) => $group->values())
            ->toArray();

        return Inertia::render('Catalog/Services/Show', [
            'service' => $serviceProfile,
            'availableSlots' => $slots,
        ]);
    }
}
