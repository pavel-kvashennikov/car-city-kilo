<?php

declare(strict_types=1);

namespace Src\Billing\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Billing\Infrastructure\Payments\PaymentGatewayInterface;
use Src\Billing\Infrastructure\Payments\YookassaGateway;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PaymentGatewayInterface::class,
            YookassaGateway::class
        );
    }

    public function boot(): void {}
}
