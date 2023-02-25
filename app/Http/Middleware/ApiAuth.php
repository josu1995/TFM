<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class ApiAuth
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
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

            if($request->route('userId') && $user->id != $request->route('userId')) {
                return response()->json(['user_invalid'], 401);
            }
            $request->attributes->add(['jwtuser' => $user]);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // Refrescamos si ha expirado
            $refreshed = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['token_expired'], $e->getStatusCode())->header('Authorization', 'Bearer ' . $refreshed);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }


        return $next($request);

    }
}
