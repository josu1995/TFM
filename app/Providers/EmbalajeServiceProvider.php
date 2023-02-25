<?php

namespace App\Providers;

use App\Models\PaqueteBusiness;
use Validator;
use Illuminate\Support\ServiceProvider;

class EmbalajeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
        Validator::extend('embalaje_business', function ($attribute, $value, $parameters, $validator) {

            if (($value != 0 && PaqueteBusiness::find($value)) || $value == 0) {
                return true;
            } else {
                return false;
            }

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