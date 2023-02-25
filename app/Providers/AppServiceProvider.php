<?php

namespace App\Providers;

use Blade;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        setlocale(LC_TIME, 'es_ES.utf8');
        // $this->app['request']->server->set('HTTPS', $this->app->environment() != 'local');
        if(env('APP_ENV') == 'local'){
            $this->app['request']->server->set('HTTP', true);
        }else{
            $this->app['request']->server->set('HTTPS', true);
        }
        

        Blade::directive('svg', function($arguments) {
            // Funky madness to accept multiple arguments into the directive
            list($path, $class) = array_pad(explode(',', trim($arguments, "() ")), 2, '');
            $path = trim($path, "' ");
            $class = trim($class, "' ");

            // Create the dom document as per the other answers
            $svg = new \DOMDocument();
            $svg->load(public_path($path));
            $svg->documentElement->setAttribute("class", $class);
            $output = $svg->saveXML($svg->documentElement);

            return $output;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        setlocale(LC_TIME, 'es');
        if ($this->app->environment() !== 'production') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
            //$this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
            //$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
