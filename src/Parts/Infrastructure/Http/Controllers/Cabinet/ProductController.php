<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\PartCategory;
use Src\Parts\Domain\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $company = $request->user()->companies()->first();
        $partsProfile = $company?->partsProfile;

        $products = $partsProfile
            ? $partsProfile->products()
                ->with('category')
                ->when($request->search, fn ($q, $s) => $q->where('name', 'ilike', "%{$s}%"))
                ->latest()
                ->paginate(20)
            : collect();

        return Inertia::render('Cabinet/Parts/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Cabinet/Parts/Products/Create', [
            'categories' => PartCategory::whereNull('parent_id')->with('children')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:500'],
            'category_id' => ['nullable', 'exists:part_categories,id'],
            'article_number' => ['nullable', 'string', 'max:100'],
            'oem_number' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used,refurbished'],
            'part_type' => ['required', 'in:original,oem,aftermarket'],
            'price_retail' => ['required', 'integer', 'min:0'],
            'price_wholesale' => ['nullable', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $company = $request->user()->companies()->first();
        $partsProfile = $company->partsProfile;

        $partsProfile->products()->create($validated);

        return redirect()->route('cabinet.parts.products.index')
            ->with('success', 'Товар добавлен.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Cabinet/Parts/Products/Edit', [
            'product' => $product,
            'categories' => PartCategory::whereNull('parent_id')->with('children')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:500'],
            'price_retail' => ['sometimes', 'integer', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:active,inactive,out_of_stock,archived'],
        ]);

        $product->update($validated);

        return back()->with('success', 'Товар обновлён.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('cabinet.parts.products.index')
            ->with('success', 'Товар удалён.');
    }
}
