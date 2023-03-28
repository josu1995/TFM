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
    protected $storesApiNamespace = 'App\Http\Controllers\API\TStore';
    protected $driversApiNamespace = 'App\Http\Controllers\API\TDriver';
    protected $businessApiNamespace = 'App\Http\Controllers\API\TBusiness';
    protected $adminNamespace = 'App\Http\Controllers\Admin';
    protected $adminBlogNamespace = 'App\Http\Controllers\Admin';
    protected $driversNamespace = 'App\Http\Controllers\Driver';
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
        $this->mapVersionApiRoutes();
        $this->mapStoresApiRoutes();
      
        $this->mapBusinessApiRoutes();

        $this->mapAdminRoutes();
        $this->mapBlogAdminRoutes();

       
        $this->mapBusinessRoutes();
        $this->mapWebRoutes();
    }

    protected function mapVersionApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->storesApiNamespace)
            ->prefix('api')
            ->group(base_path('routes/api/version.php'));
    }

    protected function mapStoresApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->storesApiNamespace)
            ->prefix('api/tstore')
            ->group(base_path('routes/api/stores.php'));
    }

    

    protected function mapBusinessApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->businessApiNamespace)
            ->prefix('api/tbusiness')
            ->group(base_path('routes/api/business.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::middleware(['web' , 'auth' , 'admin'])
            ->namespace($this->adminNamespace)
            ->prefix('administracion')
            ->group(base_path('routes/admin/admin.php'));
    }

    protected function mapBlogAdminRoutes()
    {
        Route::middleware(['web' , 'auth' , 'blog'])
            ->namespace($this->adminBlogNamespace)
            ->prefix('blog/admin')
            ->group(base_path('routes/admin/blog.php'));
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
