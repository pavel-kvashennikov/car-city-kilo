<?php

namespace Src\Dealer\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Dealer\Domain\Models\Vehicle;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'lead_type' => ['required', 'in:test_drive,credit,trade_in,callback'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'client_email' => ['nullable', 'email'],
            'preferred_datetime' => ['nullable', 'date'],
            'message' => ['nullable', 'string', 'max:1000'],
            'trade_in_data' => ['nullable', 'array'],
            'credit_data' => ['nullable', 'array'],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        DealerLead::create([
            ...$validated,
            'dealer_profile_id' => $vehicle->dealer_profile_id,
            'user_id' => $request->user()?->id,
        ]);

        return back()->with('success', 'Заявка успешно отправлена!');
    }
}
