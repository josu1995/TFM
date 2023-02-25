<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('opciones_devolucion', function($attribute, $value, $parameters, $validator) {

            if(!isset($validator->getData()['opcion_store']) && !isset($validator->getData()['opcion_domicilio'])) {
                return false;
            }
            return true;
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
