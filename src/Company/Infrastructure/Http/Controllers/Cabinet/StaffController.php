<?php

namespace Src\Company\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $staff = $company?->users()->withPivot('position')->get() ?? collect();

        return Inertia::render('Cabinet/Staff/Index', ['staff' => $staff]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Staff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'position' => ['nullable', 'string', 'max:100'],
        ]);

        $company = $request->user()->companies()->first();
        $user = User::where('email', $validated['email'])->firstOrFail();
        $company->users()->syncWithoutDetaching([$user->id => ['position' => $validated['position']]]);

        return redirect()->route('cabinet.staff.index')->with('success', 'Сотрудник добавлен.');
    }

    public function edit(int $id): Response
    {
        $user = User::findOrFail($id);

        return Inertia::render('Cabinet/Staff/Edit', ['staffMember' => $user]);
    }

    public function update(Request $request, int $id)
    {
        $company = $request->user()->companies()->first();
        $company->users()->updateExistingPivot($id, ['position' => $request->position]);

        return back()->with('success', 'Данные сотрудника обновлены.');
    }

    public function destroy(Request $request, int $id)
    {
        $company = $request->user()->companies()->first();
        $company->users()->detach($id);

        return redirect()->route('cabinet.staff.index')->with('success', 'Сотрудник удалён.');
    }
}
