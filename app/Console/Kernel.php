<?php

namespace App\Console;

use App\Models\Alerta;
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Ruta;
use Illuminate\Console\Scheduling\Schedule;
use Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\EmailFacturas::class,
        Commands\EmailAlertas::class,
        Commands\CancelarViaje::class,
        Commands\AvisoViaje::class,
        Commands\AvisoDestinatario::class,
        Commands\DevolverEnvio::class,
        Commands\TrazasEstadoTracking::class,
        Commands\FacturasBusiness::class,
        Commands\TrackingBusiness::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // Schedule para alertas
        $schedule->command('email:alertas')->dailyAt('00:00');

        // Schedule para facturas
        $schedule->command('email:facturas')->monthlyOn(1, '04:00');

        // Schedule para cancelación de viajes
        $schedule->command('viaje:cancelar')->dailyAt('21:30');

        // Schedule para aviso a transportistas
        $schedule->command('viaje:aviso')->dailyAt('22:00');

        // Schedule para aviso a destinatarios
        $schedule->command('destinatario:aviso')->dailyAt('22:00');

        // Schedule para devoluciones de envios
        $schedule->command('envio:devolver')->dailyAt('22:00');

        // Trazas estado tracking MondialRelay
        $schedule->command('tracking:business')->everyThirtyMinutes();

        // Generacion de facturas Business
        $schedule->command('facturas:business')->monthlyOn(1, '01:00');
    }
}
