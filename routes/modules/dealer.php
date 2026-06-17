<?php

use Illuminate\Support\Facades\Route;
use Src\Dealer\Infrastructure\Http\Controllers\Cabinet\LeadController as CabinetLeadController;
use Src\Dealer\Infrastructure\Http\Controllers\Cabinet\VehicleController as CabinetVehicleController;
use Src\Dealer\Infrastructure\Http\Controllers\LeadController;
use Src\Dealer\Infrastructure\Http\Controllers\VehicleCatalogController;

// Public catalog
Route::prefix('cars')->name('cars.')->group(function () {
    Route::get('/', [VehicleCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [VehicleCatalogController::class, 'show'])->name('show');
});

// Cabinet (tenant)
Route::middleware(['auth', 'tenant.active', 'set.tenant.context'])
    ->prefix('cabinet/dealer')
    ->name('cabinet.dealer.')
    ->middleware('profile:dealer')
    ->group(function () {
        Route::apiResource('vehicles', CabinetVehicleController::class);
        Route::patch('vehicles/{vehicle}/publish', [CabinetVehicleController::class, 'publish'])->name('vehicles.publish');
        Route::patch('vehicles/{vehicle}/sold', [CabinetVehicleController::class, 'markSold'])->name('vehicles.sold');
        Route::get('leads', [CabinetLeadController::class, 'index'])->name('leads.index');
        Route::patch('leads/{lead}', [CabinetLeadController::class, 'update'])->name('leads.update');
    });

// Leads (public, can be from guest)
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
