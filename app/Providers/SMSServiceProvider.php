<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Services\EnviarSMS;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EnviarSMS::class, function ($app) {
            return new EnviarSMS();
        });
    }
}
