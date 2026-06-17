<?php

declare(strict_types=1);

namespace Src\Company\Application\DTO;

use Illuminate\Http\Request;

final class CompanyDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $legalName = null,
        public readonly ?string $inn = null,
        public readonly ?string $description = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
        public readonly ?array $workingHours = null,
        public readonly ?array $socialLinks = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            legalName: $request->input('legal_name'),
            inn: $request->input('inn'),
            description: $request->input('description'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            website: $request->input('website'),
            workingHours: $request->input('working_hours'),
            socialLinks: $request->input('social_links'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'legal_name' => $this->legalName,
            'inn' => $this->inn,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'working_hours' => $this->workingHours,
            'social_links' => $this->socialLinks,
        ], fn ($value) => $value !== null);
    }
}
