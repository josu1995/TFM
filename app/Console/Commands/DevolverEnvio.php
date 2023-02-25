<?php

namespace App\Console\Commands;

use App\Events\CambiosEstado;
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Paquete;
use App\Models\Persona;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Uuid;
use Log;
use Event;

class DevolverEnvio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envio:devolver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Si existe algún envío sin recoger en destino desde hace 14 días, se activa el proceso de logística inversa';

    protected $mailService;

    /**
     * Create a new command instance.
     * @param MailService $mailService
     * @return void
     */
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $twoWeeksAgo = Carbon::today()->subDays(14);
        $envios14Dias = Envio::where([['estado_id', Estado::RECOGIDA], [DB::raw('DATE(fecha_destino)'), '<=', $twoWeeksAgo]])->with(['paquete', 'usuario', 'usuario.configuracion'])->get();

        foreach ($envios14Dias as $envio) {

            // Creamos la persona que va a recibir el envío (El propio usuario)
            $destinatario = new Persona();
            $destinatario->nombre = $envio->usuario->configuracion->nombre;
            $destinatario->apellidos = $envio->usuario->configuracion->apellidos ? $envio->usuario->configuracion->apellidos : '';
            $destinatario->email = $envio->usuario->email;
            $destinatario->telefono = $envio->usuario->configuracion->telefono;
            $destinatario->save();

            // Creamos la devolución a partir del envío original
            $devolucion = new Envio();
            $devolucion->descripcion = $envio->descripcion;
            $devolucion->codigo = Uuid::generate();
            $devolucion->localizador = $envio->localizador;
            $devolucion->precio = $envio->precio;
            $devolucion->created_at = Carbon::now();
            $devolucion->updated_at = Carbon::now();
            $devolucion->estado_id = Estado::ENTREGA;
            $devolucion->usuario_id = $envio->usuario_id;
            $devolucion->punto_recogida_id = $envio->punto_entrega_id;
            $devolucion->punto_entrega_id = $envio->punto_recogida_id;
            $devolucion->cobertura_id = $envio->cobertura_id;
            $devolucion->embalaje_id = $envio->embalaje_id;
            $devolucion->destinatario_id = $destinatario->id;
            $devolucion->pedido_id = $envio->pedido_id;
            $devolucion->precio_cobertura = $envio->precio_cobertura;
            $devolucion->envio_original_id = $envio->id;
            $devolucion->fecha_almacen = Carbon::now();

            // Modificamos el envío actual para invalidar el localizador
            $envio->localizador = $envio->localizador . '(D)';
            $envio->estado_id = Estado::DEVUELTO;
            $envio->save();
            Event::fire(new CambiosEstado($envio, Estado::find(Estado::DEVUELTO), true));
            // Guardamos la devolucion
            $devolucion->save();
            Event::fire(new CambiosEstado($devolucion, Estado::find(Estado::ENTREGA), true));

            // Duplicamos el paquete del envío original
            $paqueteDevolucion = new Paquete();
            $paqueteDevolucion->alto = $envio->paquete->alto;
            $paqueteDevolucion->ancho = $envio->paquete->ancho;
            $paqueteDevolucion->largo = $envio->paquete->largo;
            $paqueteDevolucion->peso = $envio->paquete->peso;
            $paqueteDevolucion->created_at = $envio->paquete->created_at;
            $paqueteDevolucion->updated_at = $envio->paquete->updated_at;
            $paqueteDevolucion->envio_id = $devolucion->id;
            $paqueteDevolucion->save();

            $this->mailService->cobroDevolucion($envio);

            Log::info('Envío ' . $envio->id . ' no ha sido recogido en 14 días. Devolución ' . $devolucion->id . ' preparada para viaje.');
        }

    }
}
