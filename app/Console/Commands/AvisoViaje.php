<?php

namespace App\Console\Commands;

use App\Models\Estado;
use App\Models\EstadoViaje;
use App\Models\Viaje;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AvisoViaje
 * @package App\Console\Commands
 */
class AvisoViaje extends Command
{
    /**
     * @var string
     */
    protected $signature = 'viaje:aviso';

    /**
     * @var string
     */
    protected $description = 'Aviso para transportista cuando no se han entregado ' .
        'los paquetes en destino pasados 2 días después de haberlos recogido en origen.';

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * AvisoViaje constructor.
     * @param MailService $mailService
     */
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
    }

    /**
     *
     */
    public function handle()
    {

        $antesDeAyer = Carbon::today()->subDays(2)->format('Y-m-d');

        $viajesAAvisar = Viaje::where([
            ['estado_id', EstadoViaje::RUTA],
            [DB::raw('DATE(created_at)'), '<=', $antesDeAyer]
        ])->has('envios')->whereDoesntHave('envios', function (Builder $query) {
            $query->where('estado_id', '!=', Estado::RUTA);
        })->get();

        foreach ($viajesAAvisar as $viaje) {
            $this->mailService->avisoTransportista($viaje);
        }
    }
}
