<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    /**
     * Пропускает только пользователей, у которых есть компания (резиденты рынка).
     * Покупателей перенаправляет в их кабинет.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        $hasCompany = $user->companies()->exists();

        if (! $hasCompany) {
            return redirect('/buyer/dashboard')
                ->with('error', 'Этот раздел доступен только резидентам авторынка.');
        }

        return $next($request);
    }
}
