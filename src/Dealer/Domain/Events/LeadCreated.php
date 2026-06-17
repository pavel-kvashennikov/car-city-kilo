<?php

declare(strict_types=1);

namespace Src\Dealer\Domain\Events;

use Src\Dealer\Domain\Models\DealerLead;
use Src\Shared\Domain\Events\BaseEvent;

class LeadCreated extends BaseEvent
{
    public function __construct(
        public readonly DealerLead $lead
    ) {
        parent::__construct();
    }
}
