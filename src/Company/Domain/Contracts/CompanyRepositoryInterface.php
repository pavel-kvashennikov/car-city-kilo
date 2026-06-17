<?php

declare(strict_types=1);

namespace Src\Company\Domain\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Company\Domain\Models\Company;

interface CompanyRepositoryInterface
{
    public function find(int $id): ?Company;

    public function findByUuid(string $uuid): ?Company;

    public function findBySlug(string $slug): ?Company;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Company;

    public function update(Company $company, array $attributes): bool;

    public function delete(Company $company): bool;

    public function getPendingCompanies(): LengthAwarePaginator;

    public function getActiveCompanies(): LengthAwarePaginator;
}
