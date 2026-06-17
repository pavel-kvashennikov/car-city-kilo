<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\Models\Company;
use Src\Shared\Domain\Events\BaseEvent;

class CompanyRegistered extends BaseEvent
{
    public function __construct(
        public readonly Company $company
    ) {
        parent::__construct();
    }
}
