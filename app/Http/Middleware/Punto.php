<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

// Auth
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

// Modelos
use App\Models\Usuario;

class Punto
{
    public function handle($request, Closure $next)
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        foreach ($usuario->roles as $rol) {
            if($rol->tipo == 'punto') {
                return $next($request);
            }
        }

        return response()->json(['error' => 'El usuario no es administrador de ningún punto'], 401);
    }
}
