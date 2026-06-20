<?php

use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\BillingController as AdminBillingController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CompanyManageController;
use App\Http\Controllers\Admin\MarketMapController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Buyer\AppointmentController as BuyerAppointmentController;
use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\FavoriteController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Cabinet\AnalyticsController as CabinetAnalyticsController;
use App\Http\Controllers\Cabinet\BillingController as CabinetBillingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Src\Company\Domain\Models\Company;
use Src\Company\Infrastructure\Http\Controllers\Cabinet\CompanySettingsController;
use Src\Company\Infrastructure\Http\Controllers\Cabinet\StaffController;
use Src\Company\Infrastructure\Http\Controllers\CompanyController;
use Src\Company\Infrastructure\Http\Controllers\CompanyRegistrationController;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Dealer\Infrastructure\Http\Controllers\Cabinet\VehicleController;
use Src\Dealer\Infrastructure\Http\Controllers\LeadController;
use Src\Dealer\Infrastructure\Http\Controllers\VehicleCatalogController;
use Src\Order\Infrastructure\Http\Controllers\CartController;
use Src\Parts\Domain\Models\Product;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\PartsOrderController;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\ProductController;
use Src\Parts\Infrastructure\Http\Controllers\Cabinet\ProductImportController;
use Src\Parts\Infrastructure\Http\Controllers\ProductCatalogController;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Service\Infrastructure\Http\Controllers\AppointmentController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\AppointmentManageController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\MasterController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\ScheduleController;
use Src\Service\Infrastructure\Http\Controllers\Cabinet\ServiceItemController;
use Src\Service\Infrastructure\Http\Controllers\ServiceCatalogController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Home', [
        'stats' => [
            'vehicles' => Vehicle::where('status', 'active')->count(),
            'parts' => Product::where('status', 'active')->count(),
            'services' => ServiceProfile::count(),
            'companies' => Company::where('status', 'active')->count(),
        ],
        'featuredVehicles' => Vehicle::where('status', 'active')
            ->with(['brand', 'carModel', 'photos' => fn ($q) => $q->where('is_main', true)])
            ->orderByDesc('is_featured')
            ->latest()
            ->take(6)
            ->get(),
        'featuredParts' => Product::where('status', 'active')
            ->with(['partsProfile.company', 'category'])
            ->latest()
            ->take(8)
            ->get(),
        'featuredServices' => ServiceProfile::with('company')
            ->whereHas('company', fn ($q) => $q->where('status', 'active'))
            ->take(3)
            ->get(),
    ]);
})->name('home');

// Catalog Routes
Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/cars', [VehicleCatalogController::class, 'index'])->name('cars.index');
    Route::get('/cars/{vehicle:slug}', [VehicleCatalogController::class, 'show'])->name('cars.show');

    Route::get('/parts', [ProductCatalogController::class, 'index'])->name('parts.index');
    Route::get('/parts/{product:slug}', [ProductCatalogController::class, 'show'])->name('parts.show');

    Route::get('/services', [ServiceCatalogController::class, 'index'])->name('services.index');
    Route::get('/services/{serviceProfile:slug}', [ServiceCatalogController::class, 'show'])->name('services.show');
});

// Companies
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');

// Market Map
Route::get('/market-map', [Src\MarketMap\Infrastructure\Http\Controllers\MarketMapController::class, 'index'])->name('market-map');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Company Registration
    Route::get('/register/company', [CompanyRegistrationController::class, 'create'])->name('company.register');
    Route::post('/register/company', [CompanyRegistrationController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Buyer Cabinet
    |--------------------------------------------------------------------------
    */
    Route::prefix('buyer')->name('buyer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/appointments', [BuyerAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    });

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.add');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Leads (test-drive, credit, trade-in requests)
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

    // Appointments
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    /*
    |--------------------------------------------------------------------------
    | Tenant Cabinet
    |--------------------------------------------------------------------------
    */
    Route::prefix('cabinet')->name('cabinet.')->middleware(['auth', 'tenant'])->group(function () {
        Route::get('/', [App\Http\Controllers\Cabinet\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/billing', [CabinetBillingController::class, 'index'])->name('billing');
        Route::post('/billing/subscribe', [CabinetBillingController::class, 'subscribe'])->name('billing.subscribe');
        Route::get('/analytics', [CabinetAnalyticsController::class, 'index'])->name('analytics');

        // Company Management
        Route::get('/company', [CompanySettingsController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanySettingsController::class, 'update'])->name('company.update');

        // Staff Management
        Route::resource('staff', StaffController::class)->except(['show']);

        // Dealer Profile
        Route::prefix('dealer')->name('dealer.')->group(function () {
            Route::resource('vehicles', VehicleController::class);
            // Vehicle photos
            Route::post('/vehicles/{vehicle}/photos', [Src\Dealer\Infrastructure\Http\Controllers\Cabinet\VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
            Route::put('/vehicles/{vehicle}/photos/{photo}/main', [Src\Dealer\Infrastructure\Http\Controllers\Cabinet\VehiclePhotoController::class, 'setMain'])->name('vehicles.photos.main');
            Route::delete('/vehicles/{vehicle}/photos/{photo}', [Src\Dealer\Infrastructure\Http\Controllers\Cabinet\VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');
            Route::get('/leads', [Src\Dealer\Infrastructure\Http\Controllers\Cabinet\LeadController::class, 'index'])->name('leads.index');
            Route::put('/leads/{lead}', [Src\Dealer\Infrastructure\Http\Controllers\Cabinet\LeadController::class, 'update'])->name('leads.update');
        });

        // Parts Profile
        Route::prefix('parts')->name('parts.')->group(function () {
            Route::resource('products', ProductController::class);
            // Cross-numbers
            Route::post('/products/{product}/cross-numbers', [Src\Parts\Infrastructure\Http\Controllers\Cabinet\CrossNumberController::class, 'store'])->name('products.cross_numbers.store');
            Route::delete('/products/{product}/cross-numbers/{crossNumber}', [Src\Parts\Infrastructure\Http\Controllers\Cabinet\CrossNumberController::class, 'destroy'])->name('products.cross_numbers.destroy');
            // Applicability
            Route::post('/products/{product}/applicabilities', [Src\Parts\Infrastructure\Http\Controllers\Cabinet\ApplicabilityController::class, 'store'])->name('products.applicabilities.store');
            Route::delete('/products/{product}/applicabilities/{applicability}', [Src\Parts\Infrastructure\Http\Controllers\Cabinet\ApplicabilityController::class, 'destroy'])->name('products.applicabilities.destroy');

            Route::get('/import', [ProductImportController::class, 'create'])->name('import.create');
            Route::post('/import', [ProductImportController::class, 'store'])->name('import');
            Route::get('/orders', [PartsOrderController::class, 'index'])->name('orders.index');
            Route::put('/orders/{order}', [PartsOrderController::class, 'update'])->name('orders.update');
        });

        // Service Profile
        Route::prefix('service')->name('service.')->group(function () {
            Route::resource('items', ServiceItemController::class);
            Route::resource('masters', MasterController::class);
            Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
            Route::post('/schedule/slots', [ScheduleController::class, 'generateSlots'])->name('schedule.generate');
            Route::patch('/schedule/slots/{slot}', [ScheduleController::class, 'updateSlot'])->name('schedule.slot.update');
            Route::delete('/schedule/slots/{slot}', [ScheduleController::class, 'destroySlot'])->name('schedule.slot.destroy');
            Route::get('/appointments', [AppointmentManageController::class, 'index'])->name('appointments.index');
            Route::match(['put','patch'], '/appointments/{appointment}', [AppointmentManageController::class, 'update'])->name('appointments.update');
            Route::delete('/appointments/{appointment}', [AppointmentManageController::class, 'destroy'])->name('appointments.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/companies', [CompanyManageController::class, 'index'])->name('companies.index');
        Route::put('/companies/{company}/approve', [CompanyManageController::class, 'approve'])->name('companies.approve');
        Route::put('/companies/{company}/reject', [CompanyManageController::class, 'reject'])->name('companies.reject');
        Route::put('/companies/{company}/suspend', [CompanyManageController::class, 'suspend'])->name('companies.suspend');
        Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
        Route::put('/moderation/vehicles/{vehicle}/approve', [ModerationController::class, 'approveVehicle'])->name('moderation.vehicles.approve');
        Route::put('/moderation/vehicles/{vehicle}/reject', [ModerationController::class, 'rejectVehicle'])->name('moderation.vehicles.reject');
        Route::get('/billing', [AdminBillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/market-map', [MarketMapController::class, 'index'])->name('market-map');
    });
});
