<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class DiasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('dias', function($attribute, $value, $parameters, $validator) {
            if(0 === preg_match_all("/^(L|M|X|J|V)(,(L|M|X|J|V))*$/", $value)){
                return false;
            } else {
                return true;
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
