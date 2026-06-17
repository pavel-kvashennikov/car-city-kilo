<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\ApproveCompany;

final class ApproveCompanyCommand
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $approvedBy,
    ) {}
}
