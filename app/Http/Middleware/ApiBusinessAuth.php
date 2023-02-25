<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracionBusiness;
use Closure;
use JWTAuth;

class ApiBusinessAuth
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

        if(!$request->header('Authorization')) {
            return response()->json('token_absent', 401);
        }

        if(strpos($request->header('Authorization'), 'Bearer ') === false) {
            return response()->json('token_invalid', 401);
        }

        $ecommerce = ConfiguracionBusiness::where('api_key', str_replace('Bearer ', '', $request->header('Authorization')))->first();

        if(!$ecommerce) {
            return response()->json('token_invalid', 401);
        }

        $request->attributes->add(['ecommerce' => $ecommerce]);

        return $next($request);

    }
}
