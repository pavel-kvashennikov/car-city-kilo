<?php

namespace App\Http\Middleware;

use App\Support\Seo\SeoResolver;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Parts\Domain\Models\Product;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user'    => $request->user(),
                'isAdmin' => fn () => $request->user()?->hasRole('super_admin') ?? false,
                'favoritedVehicleIds' => fn () => $request->user()
                    ?->favorites()
                    ->where('favoriteable_type', Vehicle::class)
                    ->pluck('favoriteable_id')
                    ->toArray() ?? [],
                'favoritedProductIds' => fn () => $request->user()
                    ?->favorites()
                    ->where('favoriteable_type', Product::class)
                    ->pluck('favoriteable_id')
                    ->toArray() ?? [],
            ],
            'company' => function () use ($request) {
                $company = $request->user()
                    ?->companies()
                    ->with(['businessProfiles'])
                    ->first();

                if ($company) {
                    $company->setAttribute(
                        'active_profiles',
                        $company->businessProfiles->map(fn ($p) => [
                            'type' => $p->type?->value ?? (string) $p->type,
                            'id' => $p->id,
                        ])->toArray()
                    );
                }

                return $company;
            },
            'activeProfiles' => function () use ($request) {
                $company = $request->user()
                    ?->companies()
                    ->with(['businessProfiles'])
                    ->first();

                return $company
                    ? $company->businessProfiles->map(fn ($p) => $p->type?->value ?? (string) $p->type)->values()->all()
                    : [];
            },
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'seo' => fn () => app(SeoResolver::class)->resolve($request),
            'seoSite' => fn () => [
                'name' => config('seo.site_name'),
                'url' => config('app.url'),
                'twitter' => config('seo.twitter_handle'),
            ],
        ];
    }
}
