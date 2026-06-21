<?php

namespace App\Support\Seo;

class SeoMeta
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $canonical = null,
        public readonly ?string $image = null,
        public readonly string $type = 'website',
        public readonly string $robots = 'index,follow',
        public readonly ?array $jsonLd = null,
        public readonly ?string $prev = null,
        public readonly ?string $next = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'image' => $this->image,
            'type' => $this->type,
            'robots' => $this->robots,
            'jsonLd' => $this->jsonLd,
            'prev' => $this->prev,
            'next' => $this->next,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
