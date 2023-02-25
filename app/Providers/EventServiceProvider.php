<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CambiosEstado' => [
            'App\Listeners\CambiosEstadoListener',
        ],
        'App\Events\CambiosEstadoViajes' => [
            'App\Listeners\CambiosEstadoViajesListener',
        ],
        'App\Events\PedidoRealizado' => [
            'App\Listeners\EmailPedidoRealizado',
        ],
        'App\Events\CambiosEstadoBusiness' => [
            'App\Listeners\CambiosEstadoBusinessListener',
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
