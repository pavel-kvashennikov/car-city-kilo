<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\CarBrand;
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
            'categories' => $this->flatCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:500'],
            'category_id'       => ['nullable', 'exists:part_categories,id'],
            'article_number'    => ['nullable', 'string', 'max:100'],
            'oem_number'        => ['nullable', 'string', 'max:100'],
            'barcode'           => ['nullable', 'string', 'max:100'],
            'brand'             => ['nullable', 'string', 'max:100'],
            'condition'         => ['required', 'in:new,used,refurbished'],
            'part_type'         => ['required', 'in:original,oem,aftermarket'],
            'price_retail'      => ['required', 'integer', 'min:0'],
            'price_wholesale'   => ['nullable', 'integer', 'min:0'],
            'wholesale_min_qty' => ['nullable', 'integer', 'min:1'],
            'stock_quantity'    => ['required', 'integer', 'min:0'],
            'description'       => ['nullable', 'string'],
        ]);

        $company      = $request->user()->companies()->first();
        $partsProfile = $company->partsProfile;

        $partsProfile->products()->create($validated);

        return redirect()->route('cabinet.parts.products.index')
            ->with('success', 'Товар добавлен.');
    }

    public function edit(Product $product): Response
    {
        $product->load([
            'category',
            'crossNumbers',
            'applicabilities.brand',
            'applicabilities.carModel',
        ]);

        return Inertia::render('Cabinet/Parts/Products/Edit', [
            'product'    => $product,
            'categories' => $this->flatCategories(),
            'brands'     => CarBrand::with('models')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => ['sometimes', 'string', 'max:500'],
            'category_id'       => ['nullable', 'exists:part_categories,id'],
            'article_number'    => ['nullable', 'string', 'max:100'],
            'oem_number'        => ['nullable', 'string', 'max:100'],
            'barcode'           => ['nullable', 'string', 'max:100'],
            'brand'             => ['nullable', 'string', 'max:100'],
            'condition'         => ['nullable', 'in:new,used,refurbished'],
            'part_type'         => ['nullable', 'in:original,oem,aftermarket'],
            'price_retail'      => ['sometimes', 'integer', 'min:0'],
            'price_wholesale'   => ['nullable', 'integer', 'min:0'],
            'wholesale_min_qty' => ['nullable', 'integer', 'min:1'],
            'stock_quantity'    => ['sometimes', 'integer', 'min:0'],
            'description'       => ['nullable', 'string'],
            'status'            => ['sometimes', 'in:active,inactive,out_of_stock,archived'],
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

    /** Flat list: parent categories + indented children for <select> */
    private function flatCategories(): array
    {
        $flat = [];

        foreach (PartCategory::whereNull('parent_id')->with('children')->orderBy('name')->get() as $parent) {
            $flat[] = ['id' => $parent->id, 'name' => $parent->name];
            foreach ($parent->children->sortBy('name') as $child) {
                $flat[] = ['id' => $child->id, 'name' => '— '.$child->name];
            }
        }

        return $flat;
    }
}
