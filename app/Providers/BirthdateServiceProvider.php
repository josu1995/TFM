<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class BirthdateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
        Validator::extend('birthdate', function($attribute, $value, $parameters, $validator) {
            $minAge = 18;
            return Carbon::now()->diff(new Carbon($value))->y >= $minAge;
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