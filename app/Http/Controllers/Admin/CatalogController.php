<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarModel;
use Src\Catalog\Domain\Models\PartCategory;
use Src\Billing\Domain\Models\Plan;

class CatalogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Catalog/Index', [
            'brandsCount' => CarBrand::count(),
            'modelsCount' => CarModel::count(),
            'categoriesCount' => PartCategory::count(),
        ]);
    }
}
