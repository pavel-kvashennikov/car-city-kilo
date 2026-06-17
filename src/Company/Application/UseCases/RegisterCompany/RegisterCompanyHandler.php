<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\RegisterCompany;

use Src\Company\Domain\Contracts\CompanyRepositoryInterface;
use Src\Company\Domain\Events\CompanyRegistered;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\CompanyStatus;

final class RegisterCompanyHandler
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companies
    ) {}

    public function handle(RegisterCompanyCommand $command): Company
    {
        $company = $this->companies->create([
            'owner_id' => $command->ownerId,
            'name' => $command->name,
            'legal_name' => $command->legalName,
            'inn' => $command->inn,
            'description' => $command->description,
            'phone' => $command->phone,
            'email' => $command->email,
            'website' => $command->website,
            'status' => CompanyStatus::PENDING,
        ]);

        event(new CompanyRegistered($company));

        return $company;
    }
}
