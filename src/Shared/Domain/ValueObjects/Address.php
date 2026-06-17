<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObjects;

final class Address
{
    public function __construct(
        private readonly ?string $city = null,
        private readonly ?string $street = null,
        private readonly ?string $building = null,
        private readonly ?string $office = null,
        private readonly ?string $postalCode = null,
        private readonly ?float $latitude = null,
        private readonly ?float $longitude = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            city: $data['city'] ?? null,
            street: $data['street'] ?? null,
            building: $data['building'] ?? null,
            office: $data['office'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'street' => $this->street,
            'building' => $this->building,
            'office' => $this->office,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function getFullAddress(): string
    {
        return implode(', ', array_filter([
            $this->postalCode,
            $this->city,
            $this->street,
            $this->building,
            $this->office ? "оф. {$this->office}" : null,
        ]));
    }

    public function getCoordinates(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return ['lat' => $this->latitude, 'lng' => $this->longitude];
    }
}
