<?php

namespace Src\Catalog\Infrastructure\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Catalog\Domain\Models\PartCategory;

class PartCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = PartCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }
}
