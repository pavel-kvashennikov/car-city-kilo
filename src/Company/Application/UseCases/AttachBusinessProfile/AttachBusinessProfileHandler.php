<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\AttachBusinessProfile;

use Illuminate\Support\Facades\DB;
use Src\Company\Domain\Contracts\CompanyRepositoryInterface;
use Src\Company\Domain\Models\BusinessProfile;
use Src\Dealer\Domain\Models\DealerProfile;
use Src\Parts\Domain\Models\PartsProfile;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Shared\Support\Enums\BusinessProfileType;

final class AttachBusinessProfileHandler
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companies
    ) {}

    public function handle(AttachBusinessProfileCommand $command): BusinessProfile
    {
        $company = $this->companies->find($command->companyId);

        return DB::transaction(function () use ($company, $command) {
            $profileable = match ($command->profileType) {
                BusinessProfileType::DEALER => DealerProfile::create(['company_id' => $company->id]),
                BusinessProfileType::PARTS => PartsProfile::create(['company_id' => $company->id]),
                BusinessProfileType::SERVICE => ServiceProfile::create(['company_id' => $company->id]),
            };

            return BusinessProfile::create([
                'company_id' => $company->id,
                'profile_type' => $command->profileType,
                'profileable_id' => $profileable->id,
                'profileable_type' => $profileable::class,
                'name' => $command->name,
                'is_active' => true,
            ]);
        });
    }
}
