<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Search;

use Meilisearch\Client;

class MeilisearchService
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function search(string $index, string $query, array $options = []): array
    {
        $result = $this->client->index($index)->search($query, $options);

        return [
            'hits' => $result->getHits(),
            'total' => $result->getHitsCount(),
            'processing_time_ms' => $result->getProcessingTimeMs(),
        ];
    }

    public function index(string $index, array $documents, ?string $primaryKey = null): void
    {
        $this->client->index($index)->addDocuments($documents, $primaryKey);
    }

    public function deleteDocument(string $index, int|string $documentId): void
    {
        $this->client->index($index)->deleteDocument($documentId);
    }

    public function updateSettings(string $index, array $settings): void
    {
        $this->client->index($index)->updateSettings($settings);
    }

    public function configureIndex(string $index, array $searchableAttributes, array $filterableAttributes, array $sortableAttributes = []): void
    {
        $settings = [
            'searchableAttributes' => $searchableAttributes,
            'filterableAttributes' => $filterableAttributes,
            'sortableAttributes' => $sortableAttributes,
        ];

        $this->updateSettings($index, $settings);
    }

    public function deleteIndex(string $index): void
    {
        $this->client->deleteIndex($index);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
