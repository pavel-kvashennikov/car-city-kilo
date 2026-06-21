<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Src\Company\Domain\Models\Company;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Parts\Domain\Models\Product;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Shared\Support\Enums\CompanyStatus;
use Src\Shared\Support\Enums\VehicleStatus;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate XML sitemap for public pages';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        foreach (config('seo.static_pages', []) as $path => $options) {
            $sitemap->add(
                Url::create(url($path))
                    ->setPriority($options['priority'] ?? 0.5)
                    ->setChangeFrequency($options['frequency'] ?? 'weekly')
            );
        }

        Vehicle::query()
            ->where('status', VehicleStatus::ACTIVE)
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($vehicles) use ($sitemap) {
                foreach ($vehicles as $vehicle) {
                    $sitemap->add(
                        Url::create(route('catalog.cars.show', $vehicle))
                            ->setLastModificationDate($vehicle->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.8)
                    );
                }
            });

        Product::query()
            ->where('status', 'active')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($sitemap) {
                foreach ($products as $product) {
                    $sitemap->add(
                        Url::create(route('catalog.parts.show', $product))
                            ->setLastModificationDate($product->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.7)
                    );
                }
            });

        ServiceProfile::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($services) use ($sitemap) {
                foreach ($services as $service) {
                    $sitemap->add(
                        Url::create(route('catalog.services.show', $service))
                            ->setLastModificationDate($service->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            });

        Company::query()
            ->where('status', CompanyStatus::ACTIVE)
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($companies) use ($sitemap) {
                foreach ($companies as $company) {
                    $sitemap->add(
                        Url::create(route('companies.show', $company))
                            ->setLastModificationDate($company->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });

        BlogPost::published()
            ->select(['id', 'slug', 'updated_at', 'published_at'])
            ->orderBy('id')
            ->chunkById(500, function ($posts) use ($sitemap) {
                foreach ($posts as $post) {
                    $sitemap->add(
                        Url::create(route('blog.show', $post->slug))
                            ->setLastModificationDate($post->updated_at ?? $post->published_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap written to {$path}");

        return self::SUCCESS;
    }
}
