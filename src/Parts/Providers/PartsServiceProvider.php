<?php

declare(strict_types=1);

namespace Src\Parts\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Parts\Domain\Contracts\ProductRepositoryInterface;
use Src\Parts\Infrastructure\Persistence\EloquentProductRepository;

class PartsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );
    }

    public function boot(): void {}
}
