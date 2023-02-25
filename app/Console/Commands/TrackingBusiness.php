<?php

namespace App\Console\Commands;

use App\Events\CambiosEstadoBusiness;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Models\TiposRecogidaBusiness;
use App\Models\TrazaBusinessMondialRelay;
use App\Services\Business\TrackingService;
use App\Services\MondialRelayService;
use Illuminate\Console\Command;
use Event;

class TrackingBusiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracking:business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestión tracking para envíos business';
    protected $mondialRelayService;
    protected $trackingService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MondialRelayService $mondialRelayService, TrackingService $trackingService)
    {
        parent::__construct();
        $this->mondialRelayService = $mondialRelayService;
        $this->trackingService = $trackingService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $envios = EnvioBusiness::whereNotIn('estado_id', [Estado::FINALIZADO, Estado::VALIDADO, Estado::CREADO, Estado::CANCELADO, Estado::DEVUELTO, Estado::SELECCIONADO])
            ->whereDoesntHave('devolucionAsDevolucion', function($query) {
                $query->where('finalizado', 0);
            })->get();

        foreach ($envios as $envio) {

            $this->trackingService->actualizarEstados($envio);

        }
    }
}
