<?php

namespace Src\Service\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Service\Domain\Models\Appointment;
use Src\Service\Domain\Models\TimeSlot;
use Src\Shared\Support\Enums\SlotStatus;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_profile_id' => ['required', 'exists:service_profiles,id'],
            'service_item_id' => ['nullable', 'exists:service_items,id'],
            'time_slot_id' => ['required', 'exists:time_slots,id'],
            'master_id' => ['nullable', 'exists:service_masters,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'client_email' => ['nullable', 'email'],
            'vehicle_brand' => ['nullable', 'string'],
            'vehicle_model' => ['nullable', 'string'],
            'vehicle_year' => ['nullable', 'integer'],
            'vehicle_vin' => ['nullable', 'string', 'max:17'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = DB::transaction(function () use ($validated, $request) {
            $slot = TimeSlot::where('id', $validated['time_slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($slot->status !== SlotStatus::AVAILABLE) {
                abort(422, 'Слот уже занят. Пожалуйста, выберите другое время.');
            }

            $appointment = Appointment::create([
                ...$validated,
                'user_id' => $request->user()?->id,
                'status' => 'pending',
            ]);

            $slot->update([
                'status' => SlotStatus::BOOKED,
                'appointment_id' => $appointment->id,
            ]);

            return $appointment;
        });

        return back()->with('success', 'Вы успешно записаны! Ожидайте подтверждения.');
    }
}
