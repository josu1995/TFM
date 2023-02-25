<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

// Modelos
use App\Models\Usuario;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $usuario = Auth::user();

        foreach ($usuario->roles as $rol) {
            if($rol->tipo == 'administrador') {
                return $next($request);
            }
        }

        return redirect('/inicio');
    }
}
