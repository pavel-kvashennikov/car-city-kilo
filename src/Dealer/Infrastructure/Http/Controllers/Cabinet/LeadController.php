<?php

namespace Src\Dealer\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Dealer\Domain\Models\DealerLead;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $dealerProfile = $company?->dealerProfile;

        $leads = $dealerProfile
            ? $dealerProfile->leads()
                ->with(['vehicle.brand', 'vehicle.carModel', 'vehicle.photos' => fn ($q) => $q->where('is_main', true)])
                ->when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(20)
            : collect();

        return Inertia::render('Cabinet/Dealer/Leads/Index', [
            'leads' => $leads,
            'filters' => $request->only('status'),
        ]);
    }

    public function update(Request $request, DealerLead $lead)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $lead->update($validated);

        return back()->with('success', 'Статус заявки обновлён.');
    }
}
