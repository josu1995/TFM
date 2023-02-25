<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateDrivers
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
        if (Auth::guard('driver')->guest()) {

            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                if($request->routeIs('drivers_landing_index')) {
                    return $next($request);
                } else {
                    return redirect()->guest('/drivers');
                }
            }
        } else {
            if(!Auth::guard('driver')->user()->hasRole(Rol::VIAJERO) && !Auth::guard('driver')->user()->hasRole(Rol::VIAJERO_POTENCIAL) && !Auth::guard('driver')->user()->hasRole(Rol::ADMINISTRADOR)) {
                Auth::guard('')->logout();
                return redirect()->guest('/drivers');
            } else if($request->routeIs('drivers_landing_index')) {
                return redirect()->route('drivers_inicio');
            }
        }

        return $next($request);
    }
}
