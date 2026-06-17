<?php

use Illuminate\Support\Facades\Route;
use Src\Service\Infrastructure\Http\Controllers\AppointmentController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\AppointmentManageController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\MasterController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\ScheduleController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\ServiceItemController;
use Src\Service\Infrastructure\Http\Controllers\ServiceCatalogController;

// Public catalog
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [ServiceCatalogController::class, 'show'])->name('show');
});

// Cabinet (tenant)
Route::middleware(['auth', 'tenant.active', 'set.tenant.context'])
    ->prefix('cabinet/service')
    ->name('cabinet.service.')
    ->middleware('profile:service')
    ->group(function () {
        Route::apiResource('service-items', ServiceItemController::class);
        Route::apiResource('masters', MasterController::class);
        Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule');
        Route::post('schedule/slots', [ScheduleController::class, 'generateSlots'])->name('schedule.generate');
        Route::patch('schedule/slots/{slot}', [ScheduleController::class, 'updateSlot'])->name('schedule.slots.update');
        Route::get('appointments', [AppointmentManageController::class, 'index'])->name('appointments.index');
        Route::patch('appointments/{appointment}', [AppointmentManageController::class, 'update'])->name('appointments.update');
    });

// Public appointment creation
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
