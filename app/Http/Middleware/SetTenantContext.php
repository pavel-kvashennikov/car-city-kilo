<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $company = $user->companies()->first();

            if ($company) {
                setPermissionsTeamId($company->id);
                $request->attributes->set('company', $company);
            }
        }

        return $next($request);
    }
}
