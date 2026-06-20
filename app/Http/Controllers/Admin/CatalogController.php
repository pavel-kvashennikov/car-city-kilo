<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarGeneration;
use Src\Catalog\Domain\Models\CarModel;
use Src\Catalog\Domain\Models\PartCategory;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $tab = $request->get('tab', 'brands');

        $brands = CarBrand::withCount('models')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $models = CarModel::with('brand')
            ->when($request->brand_id, fn ($q, $id) => $q->where('brand_id', $id))
            ->orderBy('name')
            ->get();

        $generations = CarGeneration::with('carModel.brand')
            ->when($request->model_id, fn ($q, $id) => $q->where('model_id', $id))
            ->orderBy('name')
            ->get();

        $categories = PartCategory::with('parent')
            ->orderByRaw('COALESCE(parent_id, 0)')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Catalog/Index', [
            'tab'         => $tab,
            'brands'      => $brands,
            'models'      => $models,
            'generations' => $generations,
            'categories'  => $categories,
            'counts'      => [
                'brands'      => $brands->count(),
                'models'      => $models->count(),
                'generations' => CarGeneration::count(),
                'categories'  => $categories->count(),
            ],
            'filters' => $request->only(['brand_id', 'model_id', 'tab']),
        ]);
    }

    // ── Brands ─────────────────────────────────────────────────────────────────

    public function storeBrand(Request $request)
    {
        $d = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'slug'       => ['nullable', 'string', 'max:120', 'unique:car_brands,slug'],
            'is_popular' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $d['slug'] = Str::slug($d['slug'] ?? $d['name']);
        CarBrand::create($d);
        return back()->with('success', 'Марка добавлена.');
    }

    public function updateBrand(Request $request, CarBrand $brand)
    {
        $d = $request->validate([
            'name'       => ['sometimes', 'string', 'max:100'],
            'slug'       => ['nullable', 'string', 'max:120', "unique:car_brands,slug,{$brand->id}"],
            'is_popular' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        if (!empty($d['slug'])) $d['slug'] = Str::slug($d['slug']);
        $brand->update($d);
        return back()->with('success', 'Марка обновлена.');
    }

    public function destroyBrand(CarBrand $brand)
    {
        if ($brand->models()->exists()) {
            return back()->with('error', 'Нельзя удалить марку — есть привязанные модели.');
        }
        $brand->delete();
        return back()->with('success', 'Марка удалена.');
    }

    // ── Models ──────────────────────────────────────────────────────────────────

    public function storeModel(Request $request)
    {
        $d = $request->validate([
            'brand_id'   => ['required', 'exists:car_brands,id'],
            'name'       => ['required', 'string', 'max:100'],
            'slug'       => ['nullable', 'string', 'max:120'],
            'is_popular' => ['boolean'],
        ]);
        $d['slug'] = Str::slug($d['slug'] ?? $d['name']);
        CarModel::create($d);
        return back()->with('success', 'Модель добавлена.');
    }

    public function updateModel(Request $request, CarModel $model)
    {
        $d = $request->validate([
            'name'       => ['sometimes', 'string', 'max:100'],
            'is_popular' => ['boolean'],
        ]);
        $model->update($d);
        return back()->with('success', 'Модель обновлена.');
    }

    public function destroyModel(CarModel $model)
    {
        if ($model->generations()->exists()) {
            return back()->with('error', 'Нельзя удалить модель — есть поколения.');
        }
        $model->delete();
        return back()->with('success', 'Модель удалена.');
    }

    // ── Generations ─────────────────────────────────────────────────────────────

    public function storeGeneration(Request $request)
    {
        $d = $request->validate([
            'model_id'  => ['required', 'exists:car_models,id'],
            'name'      => ['required', 'string', 'max:100'],
            'year_from' => ['required', 'integer', 'min:1900', 'max:2100'],
            'year_to'   => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'body_type' => ['nullable', 'string', 'max:50'],
        ]);
        CarGeneration::create($d);
        return back()->with('success', 'Поколение добавлено.');
    }

    public function destroyGeneration(CarGeneration $generation)
    {
        $generation->delete();
        return back()->with('success', 'Поколение удалено.');
    }

    // ── Part Categories ─────────────────────────────────────────────────────────

    public function storeCategory(Request $request)
    {
        $d = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:120', 'unique:part_categories,slug'],
            'parent_id'   => ['nullable', 'exists:part_categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['boolean'],
        ]);
        $d['slug'] = Str::slug($d['slug'] ?? $d['name']);
        PartCategory::create($d);
        return back()->with('success', 'Категория добавлена.');
    }

    public function updateCategory(Request $request, PartCategory $category)
    {
        $d = $request->validate([
            'name'       => ['sometimes', 'string', 'max:100'],
            'parent_id'  => ['nullable', 'exists:part_categories,id'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ]);
        $category->update($d);
        return back()->with('success', 'Категория обновлена.');
    }

    public function destroyCategory(PartCategory $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Нельзя удалить категорию — есть подкатегории.');
        }
        $category->delete();
        return back()->with('success', 'Категория удалена.');
    }
}
