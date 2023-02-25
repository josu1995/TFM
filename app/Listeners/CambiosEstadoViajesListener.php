<?php

namespace App\Listeners;

// Evento
use App\Events\CambiosEstadoViajes;

// Servicios
use App\Models\EstadoViaje;
use App\Models\NotificacionViaje;
use App\Models\Pago;
use App\Services\FacturaService;
use App\Services\MailService;

// Repositorios
use App\Repositories\OpcionRepository;

// Modelos
use App\Models\Viaje;
use App\Models\Usuario;

use DB;

class CambiosEstadoViajesListener
{
    protected $mailService;
    protected $opcionRepository;
    protected $facturaService;

    public function __construct(MailService $mailService, OpcionRepository $opcionRepository, FacturaService $facturaService)
    {
        $this->mailService = $mailService;
        $this->opcionRepository = $opcionRepository;
        $this->facturaService = $facturaService;
    }

    public function handle(CambiosEstadoViajes $evento)
    {
        $viaje = $evento->viaje;
        $usuario = $evento->viaje->transportista;
        $estado = $evento->estado;
        $texto = '';
        $ruta = $this->getRutaFromViaje($viaje->id);
        switch ($estado->id) {
            case EstadoViaje::RESERVADO:
                $texto = 'Has reservado un viaje de ' . $ruta->origen . ' a ' . $ruta->destino;
                break;
            case EstadoViaje::RUTA:
                $texto = 'Has iniciado tu ruta de ' . $ruta->origen . ' a ' . $ruta->destino;
                break;
            case EstadoViaje::FINALIZADO:
                $texto = 'Tu viaje de ' . $ruta->origen . ' a ' . $ruta->destino . ' ha sido finalizado';
                $this->facturaService->generarFacturaTransportista($viaje);
                break;
            case EstadoViaje::CANCELADO:
                $texto = 'Has cancelado tu viaje de ' . $ruta->origen . ' a ' . $ruta->destino;
                break;
            case EstadoViaje::CANCELADO_TRANSPORTER:
                $texto = 'Tu viaje de ' . $ruta->origen . ' a ' . $ruta->destino . ' ha sido cancelado por el sistema';
                break;
            default:
                break;
        }

        // Lista de acciones
        $this->crearNotificacion($viaje, $estado, $usuario, $texto);
    }

    // Creación de notificación de viaje
    private function crearNotificacion(Viaje $viaje, EstadoViaje $estado, Usuario $usuario, $texto)
    {
        $notificacion = new NotificacionViaje();
        $notificacion->texto = $texto;
        $notificacion->viaje()->associate($viaje);
        $notificacion->estado()->associate($estado);
        $notificacion->usuario_id = $usuario->id;
        $notificacion->save();
    }

    private function getRutaFromViaje($id) {

        return DB::select('select ifnull((select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_origen_id from posiciones pos where pos.viaje_id = ' . $id . ' and pos.punto_origen_id is not null limit 1))), (select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_entrega_id from envios where id=(select envio_id from envio_viaje where viaje_id = ' . $id . ' limit 1))))) as origen, ' .
            'ifnull((select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_destino_id from posiciones pos where pos.viaje_id = ' . $id . ' and pos.punto_destino_id is not null limit 1))), (select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_recogida_id from envios where id=(select envio_id from envio_viaje where viaje_id = ' . $id . ' limit 1))))) as destino')[0];

    }
    
}
