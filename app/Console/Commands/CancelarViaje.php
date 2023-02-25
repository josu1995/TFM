<?php

namespace App\Console\Commands;

use App\Events\CambiosEstadoViajes;
use App\Models\Estado;
use App\Models\EstadoViaje;
use App\Models\Viaje;
use App\Services\CalcularViaje;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Log;
use Event;

class CancelarViaje extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'viaje:cancelar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelación de viaje si no se ha producido ' .
    'la recogida en origen a las 21:30h del día posterior a la reserva.';

    protected $mailService;
    protected $calcularViaje;

    /**
     * Create a new command instance.
     *
     * @param MailService $mailService
     * @param CalcularViaje $calcularViaje
     *
     * @return void
     */
    public function __construct(MailService $mailService, CalcularViaje $calcularViaje)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->calcularViaje = $calcularViaje;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $viajesACancelar = Viaje::where([
            [DB::raw('DATE(created_at)'), '<=', $yesterday],
            ['estado_id', EstadoViaje::RESERVADO]
        ])->has('envios')->whereDoesntHave('envios', function (Builder $query) {
            $query->where('estado_id', '!=', Estado::SELECCIONADO);
        })->get();

        $viajesConEnviosEnEntrega = Viaje::where([
            [DB::raw('DATE(created_at)'), '<=', $yesterday],
            ['estado_id', EstadoViaje::RUTA]
        ])->has('envios')->whereHas('envios', function (Builder $query) {
            $query->where('estado_id', Estado::SELECCIONADO);
        })->get();

        // Damos envios en entrega o intermedio por no recogidos y los desasociamos del viaje
        foreach ($viajesConEnviosEnEntrega as $viaje) {
            $intermedioOrigen = $viaje->posiciones()
                ->whereNotNull('punto_origen_id')
                ->with(['puntoOrigen', 'puntoOrigen.localidad'])
                ->first();
            $localidadOrigen = $intermedioOrigen ?
                $intermedioOrigen->puntoOrigen->localidad_id :
                $viaje->envios[0]->puntoEntrega->localidad_id;
            $intermedioDestino = $viaje->posiciones()
                ->whereNotNull('punto_destino_id')
                ->with(['puntoDestino', 'puntoDestino.localidad'])
                ->first();
            $localidadDestino = $intermedioDestino ?
                $intermedioDestino->puntoDestino->localidad_id :
                $viaje->envios[0]->puntoRecogida->localidad_id;

            $viajeFinalizado = true;
            foreach ($viaje->envios as $envio) {
                if ($envio->estado_id == Estado::SELECCIONADO) {
                    $posicion = $envio->posiciones()
                        ->where('viaje_id', $viaje->id)
                        ->orderBy('created_at', 'desc')->first();
                    if ($envio->posiciones()
                        ->where('viaje_id', '!=', $viaje->id)
                        ->whereHas('viaje', function (Builder $query) {
                            $query->whereNotIn(
                                'estado_id',
                                array(EstadoViaje::CANCELADO, EstadoViaje::CANCELADO_TRANSPORTER)
                            );
                        })->count()) {
                        $envio->estado_id = Estado::INTERMEDIO;
//                        Event::fire(new CambiosEstado($envio, Estado::find(Estado::INTERMEDIO)));
                    } else {
                        $envio->estado_id = Estado::ENTREGA;
//                        Event::fire(new CambiosEstado($envio, Estado::find(Estado::ENTREGA)));
                    }
                    if ($posicion) {
                        $posicion->delete();
                    }
                    $viaje->envios()->detach($envio->id);
                    $envio->save();
                    Log::info(
                        'Envio ' . $envio->id . ' eliminado del viaje ' . $viaje->id . ' por no recogerlo en origen.'
                    );
                } elseif ($envio->estado_id == Estado::RUTA) {
                    $viajeFinalizado = false;
                }
            }

            $envios = $viaje->envios()->get();
            // Recalculamos precios
            $viaje->base = $this->calcularViaje->calcularViaje($envios, $localidadOrigen, $localidadDestino);
            if ($viajeFinalizado) {
                $viaje->estado_id = EstadoViaje::FINALIZADO;
                $viaje->save();
                // Modificamos el pago correspondiente
                $pago = $viaje->pagos->first();
                $pago->valor = $viaje->base - $viaje->gestion - $viaje->impuestos;
                $pago->save();
                Event::fire(new CambiosEstadoViajes($viaje, EstadoViaje::find(EstadoViaje::FINALIZADO)));
            } else {
                $viaje->save();
                // Modificamos el pago correspondiente
                $pago = $viaje->pagos->first();
                $pago->valor = $viaje->base - $viaje->gestion - $viaje->impuestos;
                $pago->save();
            }
        }

        // Cancelamos viajes
        foreach ($viajesACancelar as $viaje) {
            foreach ($viaje->envios as $envio) {
                if ($envio->posiciones()
                    ->where('viaje_id', '!=', $viaje->id)
                    ->whereHas('viaje', function (Builder $query) {
                        $query->whereNotIn(
                            'estado_id',
                            array(EstadoViaje::CANCELADO, EstadoViaje::CANCELADO_TRANSPORTER)
                        );
                    })->count()) {
                    $envio->estado_id = Estado::INTERMEDIO;
//                    Event::fire(new CambiosEstado($envio, Estado::find(Estado::INTERMEDIO)));
                } else {
                    $envio->estado_id = Estado::ENTREGA;
//                    Event::fire(new CambiosEstado($envio, Estado::find(Estado::ENTREGA)));
                }
                $envio->save();
            }

            $this->mailService->cancelarViaje($viaje);

            $viaje->estado_id = EstadoViaje::CANCELADO_TRANSPORTER;
            $viaje->fecha_finalizacion = \Carbon::now();

            $viaje->save();

            Event::fire(new CambiosEstadoViajes($viaje, EstadoViaje::find(EstadoViaje::CANCELADO_TRANSPORTER)));

            Log::info('Viaje ' . $viaje->id . ' cancelado por tiempo límite.');

        }

        Log::info('Daily schedule executed: Cancelar viaje');
    }
}
