<?php

declare(strict_types=1);

namespace Src\Order\Domain\Events;

use Src\Order\Domain\Models\Order;
use Src\Shared\Domain\Events\BaseEvent;

class OrderPlaced extends BaseEvent
{
    public function __construct(
        public readonly Order $order
    ) {
        parent::__construct();
    }
}
