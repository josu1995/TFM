<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComisionServiceProvider extends ServiceProvider
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
        $this->app->singleton(ComisionService::class, function ($app) {
            return new ComisionService();
        });
    }
}
