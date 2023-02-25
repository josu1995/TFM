<?php

namespace App\Http\Middleware;

use App\Models\Rol;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
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
        if (Auth::guard($guard)->guest()) {

            // Si se viene de la página de creación de envío
            if($request->has('crear_envio')) {
                $request->session()->put('envio_guest', $request->all());
            }

            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        } else {
            if(!Auth::user()->hasRole(Rol::CLIENTE) && !Auth::user()->hasRole(Rol::CLIENTE_POTENCIAL) && !Auth::user()->hasRole(Rol::ADMINISTRADOR)) {
                Auth::guard()->logout();
                return redirect()->guest('/');
            }
            if(is_null(Auth::user()->email) && $request->getUri() != route('permisos')) {
                return redirect(route('permisos'));
            }

        }

        return $next($request);
    }
}
