<?php

namespace App\Providers;

use App\Models\Usuario;
use Validator;
use Illuminate\Support\ServiceProvider;
use DB;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
        Validator::extend('unique_role', function($attribute, $value, $parameters, $validator) {

            $usuario = Usuario::where($attribute, $value)->with('roles')->first();
            $ret = true;
            foreach ($parameters as $parameter) {
                if ($usuario && $usuario->hasRole($parameter)) {
                    $ret = false;
                }
            }

            return $ret;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }
}