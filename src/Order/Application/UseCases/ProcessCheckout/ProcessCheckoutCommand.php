<?php

declare(strict_types=1);

namespace Src\Order\Application\UseCases\ProcessCheckout;

final class ProcessCheckoutCommand
{
    public function __construct(
        public readonly int $orderId,
    ) {}
}
