<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        // super_admin привязан к team_id = 0 (глобальный контекст)
        setPermissionsTeamId(0);

        if (! $request->user()->hasRole('super_admin')) {
            return redirect('/')->with('error', 'У вас нет прав для доступа к панели администратора.');
        }

        return $next($request);
    }
}
