<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\ApproveCompany;

use Src\Company\Domain\Contracts\CompanyRepositoryInterface;
use Src\Company\Domain\Events\CompanyApproved;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\CompanyStatus;

final class ApproveCompanyHandler
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companies
    ) {}

    public function handle(ApproveCompanyCommand $command): Company
    {
        $company = $this->companies->find($command->companyId);

        $this->companies->update($company, [
            'status' => CompanyStatus::ACTIVE,
            'verified_at' => now(),
        ]);

        $company->refresh();

        event(new CompanyApproved($company));

        return $company;
    }
}
