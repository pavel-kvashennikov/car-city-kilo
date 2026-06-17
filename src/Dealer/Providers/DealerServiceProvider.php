<?php

declare(strict_types=1);

namespace Src\Dealer\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Dealer\Domain\Contracts\VehicleRepositoryInterface;
use Src\Dealer\Infrastructure\Persistence\EloquentVehicleRepository;

class DealerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            EloquentVehicleRepository::class
        );
    }

    public function boot(): void {}
}
