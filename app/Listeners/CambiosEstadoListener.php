<?php

namespace App\Listeners;

// Evento
use App\Events\CambiosEstado;
use App\Models\Factura;
use App\Models\GastoStock;
use App\Models\Pago;
use App\Services\FacturaService;
use Event;

// Servicios
use App\Events\CambiosEstadoViajes;
use App\Models\EstadoViaje;
use App\Models\Rol;
use App\Services\MailService;

// Repositorios
use App\Repositories\OpcionRepository;

// Modelos
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Mensaje;
use App\Models\Usuario;
use App\Models\Punto;
use App\Models\Comision;
use App\Services\PinService;
use Carbon\Carbon;

class CambiosEstadoListener
{
    protected $mailService;
    protected $opcionRepository;
    protected $pinService;
    protected $facturaService;

    public function __construct(MailService $mailService, OpcionRepository $opcionRepository, PinService $pinService, FacturaService $facturaService)
    {
        $this->mailService = $mailService;
        $this->opcionRepository = $opcionRepository;
        $this->pinService = $pinService;
        $this->facturaService = $facturaService;
    }

    public function handle(CambiosEstado $evento)
    {
        $envio = $evento->envio;
        $usuario = $evento->envio->usuario;
        $estado = $evento->estado;
        $devolucion = $evento->devolucion;
        $texto = '';

        switch ($estado->id) {
            case Estado::ERROR:
                $texto = 'El envío '.$envio->codigo.' tiene algún tipo de error';
                break;
            case Estado::CREADO:
                $texto = 'Has creado un envío, pendiente de validación';
                break;
            case Estado::VALIDADO:
                $texto = 'Has creado un envío, pendiente de pago';
                break;
            case Estado::PAGADO:
                $texto = 'El pago se ha procesado correctamente.';
                if(!$usuario->hasRole(Rol::CLIENTE)) {
                    $usuario->roles()->attach(Rol::CLIENTE);
                }
                $envio -> fecha_pago = Carbon::now();
                $envio -> save();
                $this->facturaService->generarFactura($envio->pedido);
                break;
            case Estado::ENTREGA:
                $texto = 'El envío de '.$envio->descripcion.' se encuentra en '.$envio->puntoEntrega->nombre;
                $envio->fecha_almacen = Carbon::now();
                $envio->save();

                // Actualizamos stock
                // Para un envio de origen se utiliza siempre una pegatina y una bolsa si hay embalaje en el envio

                if($envio->embalaje->precio != 0) {
                    $gastoBolsas = new GastoStock();
                    $gastoBolsas->stock_id = $envio->puntoEntrega->stock->id;
                    $gastoBolsas->type = 'bolsas';
                    $gastoBolsas->qty_from = $envio->puntoEntrega->stock->bolsas;
                    $gastoBolsas->qty_to = $envio->puntoEntrega->stock->bolsas -1;
                    $gastoBolsas->bolsas = $envio->puntoEntrega->stock->bolsas -1;
                    $gastoBolsas->pegatinas = $envio->puntoEntrega->stock->pegatinas - 1;
                    $gastoBolsas->cintas = $envio->puntoEntrega->stock->cintas;
                    $gastoBolsas->save();
                    $envio->puntoEntrega->stock()->decrement('bolsas');
                }

                $gastoPegatinas = new GastoStock();
                $gastoPegatinas->stock_id = $envio->puntoEntrega->stock->id;
                $gastoPegatinas->type = 'pegatinas';
                $gastoPegatinas->qty_from = $envio->puntoEntrega->stock->pegatinas;
                $gastoPegatinas->qty_to = $envio->puntoEntrega->stock->pegatinas -1;
                $gastoPegatinas->bolsas = $envio->puntoEntrega->stock->bolsas;
                $gastoPegatinas->pegatinas = $envio->puntoEntrega->stock->pegatinas -1;
                $gastoPegatinas->cintas = $envio->puntoEntrega->stock->cintas;
                $gastoPegatinas->save();
                $envio->puntoEntrega->stock()->decrement('pegatinas');

                $envio->save();

                // Miramos si los demas envios del pedido estan en entrega para enviar el mail de alerta
                $finished = true;
                foreach ($envio->pedido->envios as $curEnvio) {
                    if($curEnvio->estado_id != Estado::ENTREGA && $envio->puntoEntrega->localidad->id == $curEnvio->puntoEntrega->localidad->id && $envio->puntoRecogida->localidad->id == $curEnvio->puntoRecogida->localidad->id) {
                        $finished = false;
                        break;
                    }
                }
                if($finished) {
                    $this->mailService->enviarAlertaEnOrigen($envio);
                }

                break;
            case Estado::RUTA:
                $this->mailService->ruta($envio);
                $texto = 'El envío de '.$envio->descripcion.' está en ruta, camino de '.$envio->puntoRecogida->localidad->nombre;
                // Miramos si todos los envios del viaje estan en ruta para asignar el estado al viaje
                $ruta = true;
                $viaje = $envio->viajes()->whereNotIn('estado_id', array(EstadoViaje::FINALIZADO, EstadoViaje::CANCELADO, EstadoViaje::CANCELADO_TRANSPORTER))->first();
                // El viaje pasa a estado ruta una vez se recoge el primer paquete
                    if($viaje && $viaje->estado_id == EstadoViaje::RESERVADO) {
                    $viaje->estado_id = EstadoViaje::RUTA;
                    $viaje->fecha_ruta = Carbon::now();
                    $viaje->save();

                    Event::fire(new CambiosEstadoViajes($viaje, EstadoViaje::find(EstadoViaje::RUTA)));
                }

                break;
            case Estado::INTERMEDIO:
                $texto = 'El envío de '.$envio->descripcion.' se encuentra en un punto intermedio, camino de '.$envio->puntoRecogida->localidad->nombre;
                $envio->fecha_intermedio = Carbon::now();
                $envio->save();
                // Miramos si los demas envios del viaje estan en entrega para enviar el mail de alerta
                $finished = true;
                foreach ($envio->viajes()->first()->envios as $curEnvio) {
                    if($curEnvio->estado_id == Estado::RUTA && $envio->puntoRecogida->localidad->id == $curEnvio->puntoRecogida->localidad->id) {
                        $finished = false;
                        break;
                    }
                }
                if($finished) {
                    $this->mailService->enviarAlertaEnIntermedio($envio);
                }
                // Mandamos mail al transporter si el viaje ha finalizado
                $viaje = $envio->viajes()->orderBy('created_at', 'desc')->first();
                $finalizado = true;
                foreach($viaje->envios as $enviotemp) {
                    if($enviotemp->estado_id != Estado::RECOGIDA && $enviotemp->estado_id != Estado::INTERMEDIO) {
                        $finalizado = false;
                        break;
                    }
                }
                if($finalizado) {
                    $viaje->estado_id = EstadoViaje::FINALIZADO;
                    $viaje->fecha_finalizacion = Carbon::now();
                    $viaje->save();
                    Event::fire(new CambiosEstadoViajes($viaje, EstadoViaje::find(EstadoViaje::FINALIZADO)));
                    $this->mailService->opinionTransportista($viaje);
                }
                break;
            case Estado::RECOGIDA:
                $texto = 'El envío de '.$envio->descripcion.' ya está en '.$envio->puntoRecogida->nombre.' ('.$envio->puntoRecogida->localidad->nombre.') esperando a que sea recogido';
                // Seteamos la fecha de recogida
                $envio->fecha_destino = Carbon::now();
                // Generamos pin para envio
                $envio->pin_recogida = $this->pinService->generatePin();
                $envio->save();
                // Envío de mail a destinatario
                $this->mailService->destinatario($envio);
                // Mandamos mail al transporter si el viaje ha finalizado
                $viaje = $envio->viajes()->orderBy('created_at', 'desc')->first();
                $finalizado = true;
                foreach($viaje->envios as $enviotemp) {
                    if($enviotemp->estado_id != Estado::RECOGIDA && $enviotemp->estado_id != Estado::INTERMEDIO) {
                        $finalizado = false;
                        break;
                    }
                }
                if($finalizado) {
                    $viaje->estado_id = EstadoViaje::FINALIZADO;
                    $viaje->fecha_finalizacion = Carbon::now();
                    $viaje->save();
                    Event::fire(new CambiosEstadoViajes($viaje, EstadoViaje::find(EstadoViaje::FINALIZADO)));
                    $this->mailService->opinionTransportista($viaje);
                }
                break;
            case Estado::FINALIZADO:
                // Guardamos fecha de finalizacion
                $envio->fecha_finalizacion = Carbon::now();
                $envio->save();
                // Creamos comisión a puntos
                $this->crearComision($envio);
                // Activamos a pendiente el pago a transportista
                $this->activarPago($envio);
                $texto = 'El envío de '.$envio->descripcion.' ya está en poder de '.$envio->destinatario->nombre;
                $this->mailService->encuesta($envio);
                $this->mailService->opinion($envio);
                break;
            case Estado::SELECCIONADO:
                $texto = 'El envío de '.$envio->descripcion.' ha sido seleccionado por un transporter y en breve comenzará el trayecto';
                break;
            case Estado::DEVUELTO:
                $texto = 'El envío de '.$envio->descripcion.' ha sido devuelto a origen';
                // Creamos comisión a puntos
                $this->crearComision($envio);
                $this->activarPago($envio);
                break;
            default:
                break;
        }

        // Lista de acciones
        $this->crearMensaje($envio, $estado, $usuario, $texto);
    }

    // Creación de mensaje o notificación a usuario
    private function crearMensaje(Envio $envio, Estado $estado, Usuario $usuario, $texto)
    {
        $mensaje = new Mensaje();
        $mensaje->texto = $texto;
        $mensaje->leido = false;
        $mensaje->envio()->associate($envio);
        $mensaje->estado()->associate($estado);
        $mensaje->usuario()->associate($usuario);
        $mensaje->save();
    }

    // Creación de comisión para puntos
    private function crearComision(Envio $envio)
    {
        $comision = new Comision();
        $comision->punto()->associate($envio->puntoEntrega);
        $comision->envio()->associate($envio);
        $comision->comision = $this->opcionRepository->getComisionPunto();
        $comision->save();

        $comision = new Comision();
        $comision->punto()->associate($envio->puntoRecogida);
        $comision->envio()->associate($envio);
        $comision->comision = $this->opcionRepository->getComisionPunto();
        $comision->save();

        if (count($envio->posicionesSinCancelar)) {
            foreach ($envio->posicionesSinCancelar as $posicion) {
                if($posicion->punto_destino_id != null) {
                    $comision = new Comision();
                    $comision->punto()->associate($posicion->puntoDestino);
                    $comision->envio()->associate($envio);
                    $comision->comision = $this->opcionRepository->getComisionPunto();
                    $comision->save();
                }
            }
        }
    }

    // Marcar los pagos como no pagados
    private function activarPago(Envio $envio)
    {
        foreach ($envio->viajesFinalizados()->whereDoesntHave('envios', function($query) {
            $query->whereNotIn('estado_id', array(Estado::FINALIZADO, Estado::DEVUELTO));
        })->get() as $viaje) {
            foreach ($viaje->pagos as $pago) {
                $pago->estado_pago = Pago::PENDIENTE;
                $pago->save();
            }
        }
    }

}
