<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Parts\Domain\Models\Product;
use Src\Parts\Domain\Models\ProductApplicability;

class ApplicabilityController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        $validated = $request->validate([
            'brand_id'  => ['required', 'exists:car_brands,id'],
            'model_id'  => ['nullable', 'exists:car_models,id'],
            'year_from' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'year_to'   => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1), 'gte:year_from'],
        ]);

        $product->applicabilities()->create($validated);

        return back()->with('success', 'Применяемость добавлена.');
    }

    public function destroy(Request $request, Product $product, ProductApplicability $applicability)
    {
        $this->authorizeProduct($request, $product);
        abort_if($applicability->product_id !== $product->id, 403);

        $applicability->delete();

        return back()->with('success', 'Применяемость удалена.');
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        $company      = $request->user()->companies()->first();
        $partsProfile = $company?->partsProfile;
        abort_if(! $partsProfile || $product->parts_profile_id !== $partsProfile->id, 403);
    }
}
