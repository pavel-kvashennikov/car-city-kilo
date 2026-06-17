<?php

use Illuminate\Support\Facades\Route;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\PartsOrderController;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\ProductController as CabinetProductController;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\ProductImportController;
use Src\Parts\Infrastructure\Http\Controllers\ProductCatalogController;

// Public catalog
Route::prefix('parts')->name('parts.')->group(function () {
    Route::get('/', [ProductCatalogController::class, 'index'])->name('index');
    Route::get('/search', [ProductCatalogController::class, 'search'])->name('search');
    Route::get('/{slug}', [ProductCatalogController::class, 'show'])->name('show');
});

// Cabinet (tenant)
Route::middleware(['auth', 'tenant.active', 'set.tenant.context'])
    ->prefix('cabinet/parts')
    ->name('cabinet.parts.')
    ->middleware('profile:parts')
    ->group(function () {
        Route::apiResource('products', CabinetProductController::class);
        Route::post('products/import', [ProductImportController::class, 'store'])->name('products.import');
        Route::get('orders', [PartsOrderController::class, 'index'])->name('orders.index');
    });
