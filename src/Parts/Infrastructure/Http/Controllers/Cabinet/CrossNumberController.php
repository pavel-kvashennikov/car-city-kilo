<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Parts\Domain\Models\CrossNumber;
use Src\Parts\Domain\Models\Product;

class CrossNumberController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $validated = $request->validate([
            'number' => ['required', 'string', 'max:100'],
            'brand'  => ['nullable', 'string', 'max:100'],
            'is_oem' => ['boolean'],
        ]);

        $product->crossNumbers()->create($validated);

        return back()->with('success', 'Кросс-номер добавлен.');
    }

    public function destroy(Request $request, Product $product, CrossNumber $crossNumber)
    {
        $this->authorizeProduct($request, $product);
        abort_if($crossNumber->product_id !== $product->id, 403);

        $crossNumber->delete();

        return back()->with('success', 'Кросс-номер удалён.');
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        $company      = $request->user()->companies()->first();
        $partsProfile = $company?->partsProfile;
        abort_if(! $partsProfile || $product->parts_profile_id !== $partsProfile->id, 403);
    }
}
