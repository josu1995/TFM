<?php

namespace App\Listeners;

// Evento
use App\Events\CambiosEstadoBusiness;

// Servicios
use App\Models\ErrorMondialRelay;
use App\Models\Estado;
use App\Models\EstadoViaje;
use App\Models\MondialRelayStore;
use App\Models\NotificacionViaje;
use App\Models\OpcionCosteDevolucionBusiness;
use App\Models\OpcionEtiquetaDevolucionBusiness;
use App\Models\Pago;
use App\Models\TipoDevolucionBusiness;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use App\Services\Business\EnvioService;
use App\Services\EnviarSMS;
use App\Services\FacturaService;
use App\Services\Business\MailService;

// Repositorios
use App\Repositories\OpcionRepository;

// Modelos
use App\Models\Viaje;
use App\Models\Usuario;

use App\Services\MondialRelayService;
use Carbon\Carbon;
use DB;

class CambiosEstadoBusinessListener
{
    protected $mailService;
    protected $opcionRepository;
    protected $facturaService;
    protected $mondialRelayService;
    protected $enviarSMS;
    protected $envioService;

    public function __construct(MailService $mailService, OpcionRepository $opcionRepository, FacturaService $facturaService, MondialRelayService $mondialRelayService, EnviarSMS $enviarSMS, EnvioService $envioService)
    {
        $this->mailService = $mailService;
        $this->opcionRepository = $opcionRepository;
        $this->facturaService = $facturaService;
        $this->mondialRelayService = $mondialRelayService;
        $this->enviarSMS = $enviarSMS;
        $this->envioService = $envioService;
    }

    public function handle(CambiosEstadoBusiness $evento)
    {
        $envios = $evento->envios;
        $estado = $evento->estado;
        $fecha = $evento->fecha;
        $texto = '';
        foreach ($envios as $envio) {

            switch ($estado->id) {
                case Estado::PAGADO:
                    if (!$envio->devolucionAsDevolucion) {
                        if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                            $origen = $envio->preferenciaRecogida;
                        } else {
                            $origen = $envio->origen;
                        }
                        if ($origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                            $response = $this->mondialRelayService->crearEnvioDualCarrier($envio);
                        } else {
                            $response = $this->mondialRelayService->crearEnvio($envio);
                        }
                        if (!$response instanceof ErrorMondialRelay) {
                            if ($fecha) {
                                $envio->fecha_pago = $fecha;
                            } else {
                                $envio->fecha_pago = \Carbon::now();
                            }
                            $envio->estado_id = Estado::PAGADO;
                            if ($envio->destino->codigoPostal->pais->iso2 != 'AT' && $envio->destino->codigoPostal->pais->iso2 != 'GB' && $envio->destino->codigoPostal->pais->iso2 != 'NL') {
                                $envio->etiqueta_preimpresa = $envio->configuracionBusiness->ajustesDevolucion->opcion_etiqueta_id == OpcionEtiquetaDevolucionBusiness::PREIMPRESA;
                            } else {
                                $envio->etiqueta_preimpresa = 0;
                            }
                            $envio->coste_cliente_devolucion = $envio->configuracionBusiness->ajustesDevolucion->opcion_coste_id == OpcionCosteDevolucionBusiness::CLIENTE;
                            $envio->plazo_devolucion = $envio->configuracionBusiness->ajustesDevolucion->plazo;
                            $envio->save();

                            if ($envio->etiqueta_preimpresa) {
                                $this->envioService->generarDevolucionPreimpresa($envio);
                            }

                            $this->mailService->envioPagado($envio);
                        }
                    }
                    break;
                case Estado::ENTREGA:
                    if ($fecha) {
                        $envio->fecha_origen = $fecha;
                    } else {
                        $envio->fecha_origen = \Carbon::now();
                    }
                    $envio->estado_id = Estado::ENTREGA;
                    $envio->save();

                    if ($envio->devolucionAsDevolucion && $envio->devolucionAsDevolucion->tipo_devolucion_id && !$envio->coste_cliente_devolucion) {
                        $this->mailService->cobroDevolucion($envio->devolucionAsDevolucion);
                    }
                    break;
                case Estado::RUTA:
                    if ($fecha) {
                        $envio->fecha_ruta = $fecha;
                    } else {
                        $envio->fecha_ruta = \Carbon::now();
                    }
                    $envio->estado_id = Estado::RUTA;
                    $envio->save();
                    break;
                case Estado::RECOGIDA:
                    if ($fecha) {
                        $envio->fecha_destino = $fecha;
                    } else {
                        $envio->fecha_destino = \Carbon::now();
                    }
                    $envio->estado_id = Estado::RECOGIDA;
                    if (!$envio->devolucionAsDevolucion || ($envio->devolucionAsDevolucion && $envio->devolucionAsDevolucion->tipo_devolucion_id != TipoDevolucionBusiness::RETORNO)) {
                        $this->mailService->envioEnDestino($envio);
                    }
                    $envio->save();
                    break;
                case Estado::REPARTO:
                    if ($fecha) {
                        $envio->fecha_destino = $fecha;
                    } else {
                        $envio->fecha_destino = \Carbon::now();
                    }
                    $envio->estado_id = Estado::REPARTO;
                    $envio->save();
                    break;
                case Estado::FINALIZADO:
                    if ($fecha) {
                        $now = $fecha;
                    } else {
                        $now = \Carbon::now();
                    }

                    $envio->fecha_finalizacion = $now;
                    if (!$envio->fecha_destino && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                        $envio->fecha_destino = $now->subHours(2)->subMinutes(random_int(0, 60));
                    }
                    $envio->estado_id = Estado::FINALIZADO;
                    if (!$envio->devolucionAsDevolucion && $envio->hasProductosReembolsables()) {
                        if ($envio->destino->codigoPostal->pais->iso2 != 'AT' && $envio->destino->codigoPostal->pais->iso2 != 'GB' && $envio->destino->codigoPostal->pais->iso2 != 'NL') {
                            if ($this->enviarSMS->validateMobilePhone($envio)) {
                                $this->enviarSMS->smsDevolucionBusiness($envio);
                            } else {
                                $this->mailService->devolucionDisponible($envio);
                            }
                        }
                    }
                    $envio->save();
                    break;
                case Estado::CANCELADO:
                    if ($fecha) {
                        $envio->fecha_finalizacion = $fecha;
                    } else {
                        $envio->fecha_finalizacion = \Carbon::now();
                    }
                    $envio->estado_id = Estado::CANCELADO;
                    $envio->save();
                    break;
                case Estado::DEVUELTO:
                    if ($fecha) {
                        $envio->fecha_finalizacion = $fecha;
                    } else {
                        $envio->fecha_finalizacion = \Carbon::now();
                    }
                    $this->envioService->generarRetorno($envio);
                    $envio->estado_id = Estado::DEVUELTO;
                    $envio->save();
                    break;
                default:
                    break;
            }
        }

        // Lista de acciones
//        $this->crearNotificacion($viaje, $estado, $usuario, $texto);
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

    private function getRutaFromViaje($id)
    {

        return DB::select('select ifnull((select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_origen_id from posiciones pos where pos.viaje_id = ' . $id . ' and pos.punto_origen_id is not null limit 1))), (select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_entrega_id from envios where id=(select envio_id from envio_viaje where viaje_id = ' . $id . ' limit 1))))) as origen, ' .
            'ifnull((select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_destino_id from posiciones pos where pos.viaje_id = ' . $id . ' and pos.punto_destino_id is not null limit 1))), (select nombre from localidades l where l.id = (select p.localidad_id from puntos p where p.id=(select punto_recogida_id from envios where id=(select envio_id from envio_viaje where viaje_id = ' . $id . ' limit 1))))) as destino')[0];

    }

}
