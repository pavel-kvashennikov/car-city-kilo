<?php

declare(strict_types=1);

namespace Src\Dealer\Infrastructure\Search;

use App\Support\MediaUrl;
use Meilisearch\Client;
use Src\Dealer\Domain\Models\Vehicle;

class VehicleSearchIndexer
{
    public function __construct(
        private readonly Client $meilisearch,
    ) {}

    public function index(Vehicle $vehicle): void
    {
        $this->meilisearch->index('vehicles')
            ->addDocuments([$this->toDocument($vehicle)]);
    }

    public function delete(Vehicle $vehicle): void
    {
        $this->meilisearch->index('vehicles')
            ->deleteDocument($vehicle->id);
    }

    public function bulkIndex(iterable $vehicles): void
    {
        $documents = [];
        foreach ($vehicles as $vehicle) {
            $documents[] = $this->toDocument($vehicle);
        }

        if (! empty($documents)) {
            $this->meilisearch->index('vehicles')
                ->addDocuments($documents);
        }
    }

    private function toDocument(Vehicle $vehicle): array
    {
        return [
            'id' => $vehicle->id,
            'uuid' => $vehicle->uuid,
            'slug' => $vehicle->slug,
            'brand_name' => $vehicle->brand?->name,
            'model_name' => $vehicle->carModel?->name,
            'generation_name' => $vehicle->generation?->name,
            'year' => $vehicle->year,
            'price' => $vehicle->price,
            'mileage' => $vehicle->mileage,
            'condition' => $vehicle->condition,
            'engine_type' => $vehicle->engine_type,
            'transmission' => $vehicle->transmission,
            'drive_type' => $vehicle->drive_type,
            'body_type' => $vehicle->body_type,
            'color' => $vehicle->color,
            'vin' => $vehicle->vin,
            'description' => $vehicle->description,
            'status' => $vehicle->status?->value ?? $vehicle->status,
            'dealer_profile_id' => $vehicle->dealer_profile_id,
            'dealer_name' => $vehicle->dealerProfile?->company?->name,
            'car_brand_id' => $vehicle->brand_id,
            'car_model_id' => $vehicle->model_id,
            'legal_status' => $vehicle->legal_status,
            'cover_photo_url' => MediaUrl::resolve($vehicle->photos->first()?->path),
            'views_count' => $vehicle->views_count ?? 0,
            'published_at' => $vehicle->published_at?->toIso8601String(),
        ];
    }
}
