<?php

use App\Providers\AppServiceProvider;
use Src\Catalog\Providers\CatalogServiceProvider;
use Src\Company\Providers\CompanyServiceProvider;
use Src\Dealer\Providers\DealerServiceProvider;
use Src\MarketMap\Providers\MarketMapServiceProvider;
use Src\Order\Providers\OrderServiceProvider;
use Src\Parts\Providers\PartsServiceProvider;
use Src\Service\Providers\ServiceServiceProvider;

return [
    AppServiceProvider::class,
    CompanyServiceProvider::class,
    DealerServiceProvider::class,
    PartsServiceProvider::class,
    ServiceServiceProvider::class,
    OrderServiceProvider::class,
    CatalogServiceProvider::class,
    MarketMapServiceProvider::class,
];
