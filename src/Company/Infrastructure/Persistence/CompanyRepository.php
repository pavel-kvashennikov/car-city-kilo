<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Company\Domain\Contracts\CompanyRepositoryInterface;
use Src\Company\Domain\Models\Company;
use Src\Shared\Infrastructure\Persistence\BaseRepository;
use Src\Shared\Support\Enums\CompanyStatus;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Company);
    }

    public function find(int $id): ?Company
    {
        return Company::find($id);
    }

    public function findByUuid(string $uuid): ?Company
    {
        return Company::where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::where('slug', $slug)->first();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Company::query();

        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    public function create(array $attributes): Company
    {
        return Company::create($attributes);
    }

    public function update(Company $company, array $attributes): bool
    {
        return $company->update($attributes);
    }

    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    public function getPendingCompanies(): LengthAwarePaginator
    {
        return Company::where('status', CompanyStatus::PENDING)
            ->latest()
            ->paginate();
    }

    public function getActiveCompanies(): LengthAwarePaginator
    {
        return Company::where('status', CompanyStatus::ACTIVE)
            ->latest()
            ->paginate();
    }
}
