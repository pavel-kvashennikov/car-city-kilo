<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Service\Domain\Models\Appointment;

class AppointmentController extends Controller
{
    public function index(Request $request): Response
    {
        $appointments = Appointment::query()
            ->where('user_id', $request->user()->id)
            ->with(['serviceProfile.company', 'serviceItem', 'master', 'timeSlot'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Buyer/Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }
}
