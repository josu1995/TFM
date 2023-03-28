<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $rootNamespace = 'App\Http\Controllers';
    protected $businessNamespace = 'App\Http\Controllers\Business';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
       
       

      
 

       
        $this->mapBusinessRoutes();
        $this->mapWebRoutes();
    }

   
   

   

  

    protected function mapBusinessRoutes()
    {
        Route::middleware('business')
            ->namespace($this->businessNamespace)
            // ->prefix('business')
            ->group(base_path('routes/business.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->rootNamespace)
            ->group(base_path('routes/web.php'));
    }
}
