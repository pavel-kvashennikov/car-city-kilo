<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $company = $user->companies()->first();

        if (! $company) {
            abort(403, 'Вы не привязаны к компании.');
        }

        if (! $company->isActive()) {
            abort(403, 'Компания не активна. Обратитесь к администрации.');
        }

        return $next($request);
    }
}
