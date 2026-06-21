<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellCarInquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SellCarInquiryController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->input('status', '');

        $query = SellCarInquiry::with('user')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest();

        return Inertia::render('Admin/SellCarInquiries/Index', [
            'inquiries' => $query->paginate(20)->withQueryString(),
            'counts' => [
                'total'       => SellCarInquiry::count(),
                'new'         => SellCarInquiry::where('status', 'new')->count(),
                'in_progress' => SellCarInquiry::where('status', 'in_progress')->count(),
                'done'        => SellCarInquiry::where('status', 'done')->count(),
            ],
            'filters' => ['status' => $status],
        ]);
    }

    public function update(Request $request, SellCarInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,done'],
        ]);

        $inquiry->update($validated);

        return back()->with('flash', ['type' => 'success', 'message' => 'Статус обновлён.']);
    }
}
