<?php

declare(strict_types=1);

namespace Src\Shared\Support\Traits;

use Illuminate\Support\Str;

trait HasSlugHelper
{
    public static function findBySlug(string $slug): ?static
    {
        return static::where('slug', $slug)->first();
    }

    public static function findBySlugOrFail(string $slug): static
    {
        return static::where('slug', $slug)->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function generateUniqueSlug(string $source): string
    {
        $slug = Str::slug($source);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
