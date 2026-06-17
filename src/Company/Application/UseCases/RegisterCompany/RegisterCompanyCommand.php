<?php

declare(strict_types=1);

namespace Src\Company\Application\UseCases\RegisterCompany;

use Illuminate\Http\Request;

final class RegisterCompanyCommand
{
    public function __construct(
        public readonly int $ownerId,
        public readonly string $name,
        public readonly ?string $legalName = null,
        public readonly ?string $inn = null,
        public readonly ?string $description = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            ownerId: $request->user()->id,
            name: $request->input('name'),
            legalName: $request->input('legal_name'),
            inn: $request->input('inn'),
            description: $request->input('description'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            website: $request->input('website'),
        );
    }
}
