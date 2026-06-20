<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
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
                'user' => $request->user(),
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
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
