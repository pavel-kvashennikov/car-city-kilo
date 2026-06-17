<?php

namespace Src\Parts\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\PartCategory;
use Src\Parts\Domain\Models\Product;

class ProductCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->where('status', 'active')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('oem_number', 'ilike', "%{$search}%")
                        ->orWhere('article_number', 'ilike', "%{$search}%")
                        ->orWhereHas('crossNumbers', fn ($cn) => $cn->where('number', 'ilike', "%{$search}%"));
                });
            })
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->condition, fn ($q, $v) => $q->where('condition', $v))
            ->when($request->price_from, fn ($q, $v) => $q->where('price_retail', '>=', $v))
            ->when($request->price_to, fn ($q, $v) => $q->where('price_retail', '<=', $v))
            ->with(['partsProfile.company', 'category'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Catalog/Parts/Index', [
            'products' => $products,
            'categories' => PartCategory::whereNull('parent_id')->with('children')->get(),
            'filters' => $request->only(['search', 'category_id', 'condition', 'price_from', 'price_to']),
        ]);
    }

    public function show(Product $product): Response
    {
        $product->increment('views_count');
        $product->load(['partsProfile.company', 'category', 'crossNumbers', 'applicabilities.brand', 'applicabilities.carModel']);

        return Inertia::render('Catalog/Parts/Show', [
            'product' => $product,
        ]);
    }
}
