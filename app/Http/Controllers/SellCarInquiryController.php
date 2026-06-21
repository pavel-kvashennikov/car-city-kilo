<?php

namespace App\Http\Controllers;

use App\Models\SellCarInquiry;
use Illuminate\Http\Request;

class SellCarInquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_info'     => ['required', 'string', 'max:255'],
            'mileage'      => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'year'         => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'client_phone' => ['required', 'string', 'max:30'],
        ]);

        $inquiry = SellCarInquiry::create([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        return back()->with('success', "Заявка №{$inquiry->id} принята! Мы перезвоним вам в течение 15 минут.");
    }
}
