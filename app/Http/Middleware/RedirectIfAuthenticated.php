<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(($request->routeIs('business_login') || $request->routeIs('business_validar_login')) && !Auth::guard('business')->check()) {
            return $next($request);
        } else if($request->routeIs('drivers_login') && !Auth::guard('driver')->check()) {
            return $next($request);
        }
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
