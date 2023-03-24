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
       
    
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

       
    }
}
