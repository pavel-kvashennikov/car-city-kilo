<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\AttachBusinessProfile;

use Src\Shared\Support\Enums\BusinessProfileType;

final class AttachBusinessProfileCommand
{
    public function __construct(
        public readonly int $companyId,
        public readonly BusinessProfileType $profileType,
        public readonly ?string $name = null,
    ) {}
}
