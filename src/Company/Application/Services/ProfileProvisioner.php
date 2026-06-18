<?php

declare(strict_types=1);

namespace Src\Company\Application\Services;

use Illuminate\Support\Str;
use Src\Company\Domain\Models\Company;
use Src\Shared\Support\Enums\BusinessProfileType;

final class ProfileProvisioner
{
    public static function provision(Company $company, string|BusinessProfileType $type): void
    {
        $type = $type instanceof BusinessProfileType ? $type : BusinessProfileType::from($type);

        $company->businessProfiles()->firstOrCreate(['type' => $type]);

        match ($type) {
            BusinessProfileType::DEALER => $company->dealerProfile()->firstOrCreate([]),
            BusinessProfileType::PARTS => $company->partsProfile()->firstOrCreate([]),
            BusinessProfileType::SERVICE => $company->serviceProfile()->firstOrCreate(
                ['company_id' => $company->id],
                ['slug' => Str::slug($company->name).'-'.$company->id],
            ),
        };
    }

    /**
     * @param  array<int, string|BusinessProfileType>  $types
     */
    public static function provisionAll(Company $company, array $types): void
    {
        foreach ($types as $type) {
            self::provision($company, $type);
        }
    }

    public static function provisionFromBusinessProfiles(Company $company): void
    {
        foreach ($company->businessProfiles as $profile) {
            self::provision($company, $profile->type);
        }
    }
}
