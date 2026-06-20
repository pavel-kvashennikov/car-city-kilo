<?php

namespace Src\Dealer\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Dealer\Domain\Models\VehiclePhoto;

class VehiclePhotoController extends Controller
{
    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'photos'   => ['required', 'array', 'max:20'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        $this->authorizeVehicle($request, $vehicle);

        $isFirst = $vehicle->photos()->count() === 0;

        foreach ($request->file('photos') as $index => $file) {
            $path = $file->store('vehicles', 'public');

            $vehicle->photos()->create([
                'path'       => $path,
                'thumb_path' => $path,
                'sort_order' => $vehicle->photos()->max('sort_order') + $index + 1,
                'is_main'    => $isFirst && $index === 0,
            ]);
        }

        return back()->with('success', 'Фотографии добавлены.');
    }

    public function setMain(Request $request, Vehicle $vehicle, VehiclePhoto $photo)
    {
        $this->authorizeVehicle($request, $vehicle);

        $vehicle->photos()->update(['is_main' => false]);
        $photo->update(['is_main' => true]);

        return back()->with('success', 'Главное фото обновлено.');
    }

    public function destroy(Request $request, Vehicle $vehicle, VehiclePhoto $photo)
    {
        $this->authorizeVehicle($request, $vehicle);

        Storage::disk('public')->delete($photo->path);
        if ($photo->thumb_path && $photo->thumb_path !== $photo->path) {
            Storage::disk('public')->delete($photo->thumb_path);
        }

        $wasMain = $photo->is_main;
        $photo->delete();

        // Назначить главным первое оставшееся фото
        if ($wasMain) {
            $vehicle->photos()->orderBy('sort_order')->first()?->update(['is_main' => true]);
        }

        return back()->with('success', 'Фото удалено.');
    }

    private function authorizeVehicle(Request $request, Vehicle $vehicle): void
    {
        $company = $request->user()->companies()->first();
        abort_if(
            ! $company || $vehicle->dealerProfile?->company_id !== $company->id,
            403,
            'Нет доступа к этому объявлению.'
        );
    }
}
