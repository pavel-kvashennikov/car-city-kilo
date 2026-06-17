<?php

namespace Src\Catalog\Infrastructure\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarModel;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = CarBrand::orderBy('is_popular', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'logo_path', 'is_popular']);

        return response()->json($brands);
    }

    public function models(CarBrand $brand): JsonResponse
    {
        $models = $brand->models()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'is_popular']);

        return response()->json($models);
    }

    public function generations(CarModel $model): JsonResponse
    {
        $generations = $model->generations()
            ->get(['id', 'name', 'year_from', 'year_to', 'body_type']);

        return response()->json($generations);
    }
}
