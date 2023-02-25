<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateBusiness
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
        if (Auth::guard('business')->guest()) {

            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                if($request->routeIs('business_landing_index')) {
                    return $next($request);
                } else {
                    // return redirect()->guest('/business');
                    return redirect()->guest('/');
                }
            }
        } else {
            if(!Auth::guard('business')->user()->hasRole(Rol::VIAJERO) && !Auth::guard('business')->user()->hasRole(Rol::BUSINESS) && !Auth::guard('business')->user()->hasRole(Rol::ADMINISTRADOR)) {
                Auth::guard('')->logout();
                // return redirect()->guest('/business');
                return redirect()->guest('/');
            } else if($request->routeIs('business_landing_index')) {
                return redirect()->route('business_envios_pendientes_pago');
            }
        }

        return $next($request);
    }
}
