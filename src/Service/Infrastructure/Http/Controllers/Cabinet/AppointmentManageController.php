<?php

namespace Src\Service\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\Appointment;

class AppointmentManageController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $serviceProfile = $company?->serviceProfile;

        $appointments = $serviceProfile
            ? $serviceProfile->appointments()
                ->with(['serviceItem', 'master', 'timeSlot', 'user'])
                ->when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(20)
            : collect();

        return Inertia::render('Cabinet/Service/Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,in_progress,completed,cancelled,no_show'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $appointment->update($validated);

        return back()->with('success', 'Статус записи обновлён.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return back()->with('success', 'Запись удалена.');
    }
}
