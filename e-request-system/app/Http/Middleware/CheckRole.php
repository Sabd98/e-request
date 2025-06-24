<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check())
            return redirect('login');

        $userRole = auth()->user()->role;

        if (in_array($userRole, $roles))
            return $next($request);

        abort(403, 'Unauthorized access');
    }
}
