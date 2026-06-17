<?php

use Illuminate\Support\Facades\Route;
use Src\Catalog\Infrastructure\Http\Controllers\Api\BrandController;
use Src\Catalog\Infrastructure\Http\Controllers\Api\PartCategoryController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public catalog endpoints
    Route::get('/catalog/brands', [BrandController::class, 'index']);
    Route::get('/catalog/brands/{brand}/models', [BrandController::class, 'models']);
    Route::get('/catalog/models/{model}/generations', [BrandController::class, 'generations']);
    Route::get('/catalog/part-categories', [PartCategoryController::class, 'index']);
});
