<?php

declare(strict_types=1);

namespace Src\Service\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Service\Domain\Contracts\AppointmentRepositoryInterface;
use Src\Service\Infrastructure\Persistence\EloquentAppointmentRepository;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AppointmentRepositoryInterface::class,
            EloquentAppointmentRepository::class
        );
    }

    public function boot(): void {}
}
