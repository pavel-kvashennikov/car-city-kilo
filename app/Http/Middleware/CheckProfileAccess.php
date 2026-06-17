<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileAccess
{
    public function handle(Request $request, Closure $next, string $profileType): Response
    {
        $company = $request->attributes->get('company');

        if (! $company) {
            abort(403, 'Компания не определена.');
        }

        $profile = $company->businessProfiles()
            ->where('profile_type', $profileType)
            ->where('is_active', true)
            ->first();

        if (! $profile) {
            abort(403, "Профиль «{$profileType}» не активирован для вашей компании.");
        }

        $request->attributes->set('business_profile', $profile);

        return $next($request);
    }
}
