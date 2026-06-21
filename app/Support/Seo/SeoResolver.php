<?php

namespace App\Support\Seo;

use App\Models\BlogPost;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Src\Company\Domain\Models\Company;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Parts\Domain\Models\Product;
use Src\Service\Domain\Models\ServiceProfile;

class SeoResolver
{
    public function resolve(Request $request): array
    {
        if ($this->shouldNoIndex($request)) {
            return $this->noIndex($request)->toArray();
        }

        $routeName = $request->route()?->getName();

        return match ($routeName) {
            'home' => $this->forHome()->toArray(),
            'catalog.cars.index' => $this->forListing(
                'Каталог автомобилей',
                'Новые и подержанные автомобили от проверенных дилеров городского авторынка. Фильтры по марке, году, цене и пробегу.',
                route('catalog.cars.index'),
            )->toArray(),
            'catalog.cars.show' => $this->forVehicle($request->route('vehicle'))->toArray(),
            'catalog.parts.index' => $this->forListing(
                'Каталог запчастей',
                'Оригинальные и аналоговые автозапчасти с поиском по OEM-номеру и артикулу от резидентов авторынка.',
                route('catalog.parts.index'),
            )->toArray(),
            'catalog.parts.show' => $this->forProduct($request->route('product'))->toArray(),
            'catalog.services.index' => $this->forListing(
                'Автосервисы',
                'СТО и автосервисы на городском авторынке. Онлайн-запись к мастерам, цены на услуги.',
                route('catalog.services.index'),
            )->toArray(),
            'catalog.services.show' => $this->forService($request->route('serviceProfile'))->toArray(),
            'companies.index' => $this->forListing(
                'Компании авторынка',
                'Дилеры, магазины запчастей и автосервисы — резиденты городского авторынка.',
                route('companies.index'),
            )->toArray(),
            'companies.show' => $this->forCompany($request->route('company'))->toArray(),
            'market-map' => $this->forListing(
                'Карта авторынка',
                'Интерактивная схема городского авторынка. Найдите павильон, дилера или сервис на карте.',
                route('market-map'),
            )->toArray(),
            'sell-car' => $this->forListing(
                'Продать автомобиль',
                'Быстрая продажа авто через дилеров городского авторынка. Оценка, Trade-in и выкуп.',
                route('sell-car'),
            )->toArray(),
            'blog.index' => $this->forBlogIndex($request)->toArray(),
            'blog.show' => $this->forBlogPost($request->route('slug'))->toArray(),
            default => $this->forDefault($request)->toArray(),
        };
    }

    private function shouldNoIndex(Request $request): bool
    {
        $routeName = $request->route()?->getName() ?? '';

        foreach (config('seo.noindex_route_prefixes', []) as $prefix) {
            if (str_starts_with($routeName, $prefix) || $routeName === $prefix) {
                return true;
            }
        }

        foreach (config('seo.noindex_paths', []) as $path) {
            $path = ltrim($path, '/');
            if ($request->path() === $path || str_starts_with($request->path(), $path.'/')) {
                return true;
            }
        }

        return false;
    }

    private function noIndex(Request $request): SeoMeta
    {
        return new SeoMeta(
            title: $this->formatTitle(config('seo.site_name')),
            description: config('seo.default_description'),
            canonical: $request->url(),
            image: $this->defaultImage(),
            robots: 'noindex,nofollow',
        );
    }

    private function forHome(): SeoMeta
    {
        $siteName = config('seo.site_name');
        $tagline = config('seo.tagline');

        return new SeoMeta(
            title: $this->formatTitle("{$siteName} — {$tagline}"),
            description: config('seo.default_description'),
            canonical: url('/'),
            image: $this->defaultImage(),
            type: 'website',
            jsonLd: [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebSite',
                        'name' => $siteName,
                        'url' => url('/'),
                        'description' => config('seo.default_description'),
                        'potentialAction' => [
                            '@type' => 'SearchAction',
                            'target' => [
                                '@type' => 'EntryPoint',
                                'urlTemplate' => route('catalog.cars.index').'?search={search_term_string}',
                            ],
                            'query-input' => 'required name=search_term_string',
                        ],
                    ],
                    [
                        '@type' => 'Organization',
                        'name' => $siteName,
                        'url' => url('/'),
                        'logo' => $this->defaultImage(),
                        'description' => config('seo.default_description'),
                    ],
                ],
            ],
        );
    }

    private function forListing(string $title, string $description, string $canonical): SeoMeta
    {
        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $canonical,
            image: $this->defaultImage(),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'description' => $description,
                'url' => $canonical,
            ],
        );
    }

    private function forVehicle(?Vehicle $vehicle): SeoMeta
    {
        if (! $vehicle) {
            return $this->forDefault(request());
        }

        $vehicle->loadMissing(['brand', 'carModel', 'photos']);

        $name = trim(implode(' ', array_filter([
            $vehicle->brand?->name,
            $vehicle->carModel?->name,
            $vehicle->year,
        ])));

        $title = $vehicle->seo_title ?: "{$name} — {$vehicle->price_formatted}";
        $description = $vehicle->seo_description
            ?: Str::limit(strip_tags($vehicle->description ?? "Купить {$name} на городском авторынке. Цена: {$vehicle->price_formatted}."), 160);

        $image = $this->resolveVehicleImage($vehicle);
        $url = route('catalog.cars.show', $vehicle);

        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $url,
            image: $image,
            type: 'product',
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Car',
                'name' => $name,
                'description' => $description,
                'url' => $url,
                'image' => $image ? [$image] : null,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $vehicle->brand?->name,
                ],
                'modelDate' => (string) $vehicle->year,
                'mileageFromOdometer' => $vehicle->mileage ? [
                    '@type' => 'QuantitativeValue',
                    'value' => $vehicle->mileage,
                    'unitCode' => 'KMT',
                ] : null,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $vehicle->price,
                    'priceCurrency' => 'RUB',
                    'availability' => 'https://schema.org/InStock',
                    'url' => $url,
                ],
            ],
        );
    }

    private function forProduct(?Product $product): SeoMeta
    {
        if (! $product) {
            return $this->forDefault(request());
        }

        $product->loadMissing(['partsProfile.company', 'category']);

        $title = $product->seo_title ?: $product->name;
        $description = $product->seo_description
            ?: Str::limit(strip_tags($product->description ?? "Купить {$product->name} на авторынке. Цена: {$product->price_formatted}."), 160);

        $image = $product->cover_photo_url ?: $this->defaultImage();
        $url = route('catalog.parts.show', $product);

        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $url,
            image: $image,
            type: 'product',
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product->name,
                'description' => $description,
                'url' => $url,
                'image' => $image ? [$image] : null,
                'sku' => $product->article_number ?: $product->oem_number,
                'brand' => $product->brand ? [
                    '@type' => 'Brand',
                    'name' => $product->brand,
                ] : null,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $product->price_retail,
                    'priceCurrency' => 'RUB',
                    'availability' => ($product->stock_quantity ?? 0) > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'url' => $url,
                ],
            ],
        );
    }

    private function forService(?ServiceProfile $service): SeoMeta
    {
        if (! $service) {
            return $this->forDefault(request());
        }

        $service->loadMissing('company');

        $companyName = $service->company?->name ?? 'Автосервис';
        $title = "{$companyName} — автосервис";
        $description = Str::limit(strip_tags(
            $service->description ?? "Автосервис {$companyName} на городском авторынке. Онлайн-запись, услуги и мастера."
        ), 160);

        $image = MediaUrl::resolve($service->company?->logo_path) ?: $this->defaultImage();
        $url = route('catalog.services.show', $service);

        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $url,
            image: $image,
            type: 'website',
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'AutoRepair',
                'name' => $companyName,
                'description' => $description,
                'url' => $url,
                'image' => $image,
                'telephone' => $service->company?->phone,
                'email' => $service->company?->email,
            ],
        );
    }

    private function forCompany(?Company $company): SeoMeta
    {
        if (! $company) {
            return $this->forDefault(request());
        }

        $title = $company->name;
        $description = Str::limit(strip_tags(
            $company->description ?? "Компания {$company->name} — резидент городского авторынка."
        ), 160);

        $image = MediaUrl::resolve($company->logo_path) ?: $this->defaultImage();
        $url = route('companies.show', $company);

        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $url,
            image: $image,
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => $company->name,
                'description' => $description,
                'url' => $url,
                'image' => $image,
                'telephone' => $company->phone,
                'email' => $company->email,
            ],
        );
    }

    private function forBlogIndex(Request $request): SeoMeta
    {
        $title = 'Блог';
        $description = 'Новости, советы и обзоры автомобильного рынка от экспертов городского авторынка.';
        $canonical = route('blog.index');

        if ($category = $request->query('category')) {
            $title = "Блог — {$category}";
            $canonical = route('blog.index', ['category' => $category]);
        }

        return new SeoMeta(
            title: $this->formatTitle($title),
            description: $description,
            canonical: $canonical,
            image: $this->defaultImage(),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => config('seo.site_name').' — Блог',
                'description' => $description,
                'url' => route('blog.index'),
            ],
        );
    }

    private function forBlogPost(?string $slug): SeoMeta
    {
        if (! $slug) {
            return $this->forDefault(request());
        }

        $post = BlogPost::published()
            ->with(['author', 'category'])
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            return $this->forDefault(request());
        }

        $description = Str::limit(strip_tags($post->excerpt ?: $post->content), 160);
        $image = $post->thumbnail
            ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : url($post->thumbnail))
            : $this->defaultImage();
        $url = route('blog.show', $post->slug);

        return new SeoMeta(
            title: $this->formatTitle($post->title),
            description: $description,
            canonical: $url,
            image: $image,
            type: 'article',
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $post->title,
                'description' => $description,
                'url' => $url,
                'image' => $image ? [$image] : null,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $post->author?->name ?? config('seo.site_name'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('seo.site_name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $this->defaultImage(),
                    ],
                ],
            ],
        );
    }

    private function forDefault(Request $request): SeoMeta
    {
        return new SeoMeta(
            title: $this->formatTitle(config('seo.site_name')),
            description: config('seo.default_description'),
            canonical: $request->url(),
            image: $this->defaultImage(),
        );
    }

    private function formatTitle(string $title): string
    {
        $siteName = config('seo.site_name');
        $separator = config('seo.title_separator', ' — ');

        if (Str::contains($title, $siteName)) {
            return $title;
        }

        return $title.$separator.$siteName;
    }

    private function defaultImage(): ?string
    {
        $image = config('seo.default_image');

        if ($image) {
            return str_starts_with($image, 'http') ? $image : url($image);
        }

        return url('/images/og-default.svg');
    }

    private function resolveVehicleImage(Vehicle $vehicle): ?string
    {
        $photo = $vehicle->photos->first();

        if ($photo) {
            return MediaUrl::resolve($photo->url ?? $photo->path);
        }

        return $this->defaultImage();
    }
}
