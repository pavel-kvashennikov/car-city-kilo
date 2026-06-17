<?php

declare(strict_types=1);

namespace Src\Company\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Company\Domain\Contracts\CompanyRepositoryInterface;
use Src\Company\Infrastructure\Persistence\CompanyRepository;

class CompanyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );
    }

    public function boot(): void {}
}
