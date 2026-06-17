<?php

namespace Src\Service\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\TimeSlot;
use Src\Shared\Support\Enums\SlotStatus;

class ScheduleController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $serviceProfile = $company?->serviceProfile;

        $slots = $serviceProfile
            ? $serviceProfile->timeSlots()
                ->where('date', '>=', now()->startOfDay())
                ->with('master')
                ->orderBy('date')
                ->orderBy('start_time')
                ->paginate(50)
            : collect();

        return Inertia::render('Cabinet/Service/Schedule/Index', [
            'slots' => $slots,
            'masters' => $serviceProfile?->masters ?? collect(),
        ]);
    }

    public function generateSlots(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date', 'after_or_equal:today'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration' => ['required', 'integer', 'min:15', 'max:480'],
            'master_id' => ['nullable', 'exists:service_masters,id'],
        ]);

        $company = $request->user()->companies()->first();
        $serviceProfile = $company->serviceProfile;

        $period = CarbonPeriod::create($request->date_from, $request->date_to);
        $slotsCreated = 0;

        foreach ($period as $date) {
            $currentTime = Carbon::parse($date->format('Y-m-d').' '.$request->start_time);
            $endTime = Carbon::parse($date->format('Y-m-d').' '.$request->end_time);

            while ($currentTime->copy()->addMinutes($request->slot_duration)->lte($endTime)) {
                TimeSlot::firstOrCreate([
                    'service_profile_id' => $serviceProfile->id,
                    'master_id' => $request->master_id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $currentTime->format('H:i'),
                    'end_time' => $currentTime->copy()->addMinutes($request->slot_duration)->format('H:i'),
                ], [
                    'status' => SlotStatus::AVAILABLE,
                ]);

                $currentTime->addMinutes($request->slot_duration);
                $slotsCreated++;
            }
        }

        return back()->with('success', "Создано {$slotsCreated} слотов.");
    }
}
