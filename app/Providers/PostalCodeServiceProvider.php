<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class PostalCodeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
      Validator::extend('postcode', function($attribute, $value, $parameters, $validator) {

          return preg_match('/^[0-9]{5}(\-[0-9]{4})?$/', $value);

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