<?php

namespace App\Services\Business;

// Utilidades
use App\Models\CodigoPostal;
use App\Models\DestinatarioBusiness;
use App\Models\DestinoBusiness;
use App\Models\DevolucionBusiness;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Models\FacturaDevolucionBusiness;
use App\Models\MondialRelayStore;
use App\Models\OpcionCosteDevolucionBusiness;
use App\Models\OrigenBusiness;
use App\Models\Pais;
use App\Models\PaqueteBusiness;
use App\Models\PedidoBusiness;
use App\Models\ProductoBusiness;
use App\Models\ProductoEnvioBusiness;
use App\Models\Punto;
use App\Models\Rango;
use App\Models\RangoBusiness;
use App\Models\TarifaBusiness;
use App\Models\TarifaZonaRangoBusiness;
use App\Models\TipoDevolucionBusiness;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use App\Repositories\OpcionRepository;
use App\Services\MondialRelayService;
use Config;
use Uuid;
use Auth;

class EnvioService
{

    protected $opciones;
    protected $mondialRelayService;
    protected $facturaService;
    protected $mailService;

    public function __construct(OpcionRepository $opciones, MondialRelayService $mondialRelayService, FacturaService $facturaService, MailService $mailService)
    {
        $this->opciones = $opciones;
        $this->mondialRelayService = $mondialRelayService;
        $this->facturaService = $facturaService;
        $this->mailService = $mailService;
    }

    public function calcularPrecio($envioId)
    {
        $envio = EnvioBusiness::find($envioId);

        $ecommerce = $envio->configuracionBusiness;

        // Primero encontramos el rango
        $peso = 0;
        foreach ($envio->productos as $producto) {
            $peso += $producto->peso * $producto->pivot->cantidad;
        }

        $rango = RangoBusiness::where([['min', '<', $peso], ['max', '>=', $peso]])->first();

        // Establecemos la zona
        if ($envio->destino->codigoPostal->zonaEspecial && $envio->destino->codigoPostal->zonaEspecial->zona) {
            $zona = $envio->destino->codigoPostal->zonaEspecial->zona;
        } else {
            $zona = $envio->destino->codigoPostal->pais->zona;
        }

        $rel = TarifaZonaRangoBusiness::where([['zona_id', $zona->id], ['rango_id', $rango->id], ['tarifa_id', $ecommerce->tarifa->id]])->first();

        $precio = $rel->precio;

        // 10% de descuento si el destino es store
        if ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE) {
            $precio = $precio - ($precio * 0.1);
        }

        if ($envio->destino->codigoPostal->zonaEspecial) {
            $precio += $envio->destino->codigoPostal->zonaEspecial->comision;
        }

        // Sumamos el IVA
//        $precio += number_format($precio * $this->opciones->getImpuestos()/100, 2);

        return $precio;
    }

    public function calcularPrecioDevolucion($peso, $envioId, $tipo_devolucion)
    {
        $envio = EnvioBusiness::find($envioId);

        $rango = RangoBusiness::where([['min', '<', $peso], ['max', '>=', $peso]])->first();

        // Establecemos la zona
        $destino = null;
        if ($envio->destino->codigoPostal->zonaEspecial && $envio->destino->codigoPostal->zonaEspecial->zona) {
            $zona = $envio->destino->codigoPostal->zonaEspecial->zona;
        } else {
            $zona = $envio->destino->codigoPostal->pais->zona;
        }

        if ($tipo_devolucion == TipoDevolucionBusiness::DEVOLUCION && $envio->coste_cliente_devolucion) {
            $rel = TarifaZonaRangoBusiness::where([['zona_id', $zona->id], ['rango_id', $rango->id], ['tarifa_id', TarifaBusiness::where('nombre', env('TARIFA_DEVOLUCION_BUSINESS'))->first()->id]])->first();
        } else {
            $rel = TarifaZonaRangoBusiness::where([['zona_id', $zona->id], ['rango_id', $rango->id], ['tarifa_id', $envio->configuracionBusiness->tarifa->id]])->first();
        }

        $precio = $rel->precio;

        if ($tipo_devolucion == TipoDevolucionBusiness::DEVOLUCION && !$envio->coste_cliente_devolucion) {
            if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                $origen = $envio->preferenciaRecogida;
            } else {
                $origen = $envio->origen;
            }

            if ($origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                $precio = $precio - ($precio * 0.1);
            }
        }

        if ($envio->destino->codigoPostal->zonaEspecial) {
            $precio += $envio->destino->codigoPostal->zonaEspecial->comision;
        }

        // Sumamos el IVA
//        $precio += number_format($precio * $this->opciones->getImpuestos()/100, 2);

        return $precio;
    }

    public function createEnvio($request)
    {
        $usuario = Auth::guard('business')->user();

        $envio = new EnvioBusiness();
        $envio->configuracion_business_id = $usuario->configuracionBusiness->id;
        $envio->codigo = Uuid::generate()->string;
        if (!$request->cp_origen_id) {
            $envio->tipo_origen_id = TipoOrigenBusiness::PREFERENCIA;
            $envio->origen_id = $usuario->configuracionBusiness->preferenciaRecogida->id;
        } else {
            $envio->tipo_origen_id = TipoOrigenBusiness::NUEVO;
            $origen = new OrigenBusiness();
            $origen->cp_id = $request->cp_origen_id;
            $origen->tipo_recogida_id = $request->tipo_recogida_id;
            if ($origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origen->direccion = $request->domicilio_origen;
            } else if ($origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                $origen->tipo_store_id = $request->punto_origen_tipo;
                if ($request->punto_origen_tipo == Config::get('enums.tiposStores.puntoPack')) {
                    $origen->store_id = substr($request->punto_origen_id, 1);
                    $origen->cp_id = MondialRelayStore::find($origen->store_id)->cp_id;

                } else {
                    $origen->store_id = substr($request->punto_origen_id, strlen($request->punto_origen_id) - 2);
                    $origen->cp_id = Punto::find($origen->store_id)->codigoPostal->id;
                }
            } else {
                return redirect()->back()->with(['error' => 'Datos incorrectos.'])->withInput();
            }
            $origen->save();
            $envio->origen_id = $origen->id;
        }

        if ($request->referencia_pedido) {
            $pedido = new PedidoBusiness();
            $pedido->configuracion_business_id = $usuario->configuracionBusiness->id;
            $pedido->num_pedido = $request->referencia_pedido;
            $pedido->save();
            $envio->pedido_id = $pedido->id;
        }

        if ($request->embalaje != 0) {
            $envio->paquete_id = $request->embalaje;
        } else {
            $paquete = new PaqueteBusiness();
            $paquete->alto = $request->alto;
            $paquete->ancho = $request->ancho;
            $paquete->largo = $request->largo;
            $paquete->save();
            $envio->paquete_id = $paquete->id;
        }

        $envio->estado_id = Estado::VALIDADO;

        $destinatario = new DestinatarioBusiness();
        $destinatario->nombre = $request->nombre;
        $destinatario->apellidos = $request->apellidos;
        $destinatario->email = $request->email;
        $destinatario->pais_id = $request->prefijo;
        $destinatario->telefono = $request->telefono;
        $destinatario->save();

        $envio->destinatario_id = $destinatario->id;

        $destino = new DestinoBusiness();
        $destino->pais_id = $request->pais_destino_id;
        $destino->cp_id = $request->cp_destino_id;
        $destino->tipo_entrega_id = $request->tipo_entrega_destino_id;
        if ($destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $destino->direccion = $request->direccion_destino;
        } else if ($destino->tipo_entrega_id == TiposRecogidaBusiness::STORE) {
            $destino->tipo_store_id = $request->punto_destino_tipo;
            if ($destino->tipo_store_id == Config::get('enums.tiposStores.puntoPack')) {
                $destino->store_id = substr($request->punto_destino_id, 1);
                $destino->cp_id = MondialRelayStore::find($destino->store_id)->cp_id;
            } else {
                $destino->store_id = substr($request->punto_destino_id, strlen($request->punto_destino_id) - 2);
                $destino->cp_id = Punto::find($destino->store_id)->codigoPostal->id;
            }
        }
        $destino->save();
        $envio->destino_id = $destino->id;

        $envio->save();

        for ($i = 0; $i < count($request->nombre_producto); $i++) {
            $rel = new ProductoEnvioBusiness();
            if (strpos($request->nombre_producto[$i], '-') !== false && ProductoBusiness::where([['configuracion_business_id', $usuario->configuracionBusiness->id], ['referencia', trim(explode(' - ', $request->nombre_producto[$i])[0])]])->count()) {
                $nombre = trim(explode(' - ', $request->nombre_producto[$i])[1]);
            } else {
                $nombre = trim($request->nombre_producto[$i]);
            }
            $producto = ProductoBusiness::where([['nombre', $nombre], ['configuracion_business_id', $usuario->configuracionBusiness->id]])->first();

            $newProducto = new ProductoBusiness();
            if ($producto) {
                $newProducto->nombre = $producto->nombre;
            } else {
                $newProducto->nombre = $request->nombre_producto[$i];
            }
            $newProducto->peso = $request->peso_producto[$i];
            $newProducto->reembolsable = true;

            if (!$producto || $producto->peso != $newProducto->peso) {
                $newProducto->save();
                $rel->producto_id = $newProducto->id;
            } else {
                $rel->producto_id = $producto->id;
            }
            $rel->envio_id = $envio->id;
            $rel->cantidad = $request->num_productos[$i];
            $rel->save();
        }

        $envio->precio = $this->calcularPrecio($envio->id);
        $envio->impuestos = number_format($envio->precio * $this->opciones->getImpuestos() / 100, 2);

        $envio->save();
    }

    public function createEnvioFromXls($envioReq)
    {
        $usuario = Auth::guard('business')->user();

        $envio = new EnvioBusiness();
        $envio->configuracion_business_id = $usuario->configuracionBusiness->id;
        $envio->codigo = Uuid::generate()->string;

        $envio->tipo_origen_id = TipoOrigenBusiness::NUEVO;
        // Origen
        $origen = new OrigenBusiness();
        $origen->cp_id = CodigoPostal::where('codigo_postal', $envioReq['cp_origen'])->whereHas('pais', function ($query) {
            $query->whereNotNull('zona_id');
        })->first()->id;
        if ($envioReq['tipo_recogida'] == 'S') {
            $origen->tipo_recogida_id = TiposRecogidaBusiness::STORE;
            $tipoStore = substr($envioReq['store_origen'], 0, 1);
            if ($tipoStore == Config::get('enums.tiposStores.puntoPack')) {
                $idStore = substr($envioReq['store_origen'], 1);
            } elseif ($tipoStore == Config::get('enums.tiposStores.transporter')) {
                $idStore = substr($envioReq['store_origen'], sizeof($envioReq['store_origen']) - 2);
            } else {
                return redirect()->back()->with(['error' => 'Datos incorrectos.']);
            }
            $origen->tipo_store_id = $tipoStore;
            $origen->store_id = $idStore;
        } elseif ($envioReq['tipo_recogida'] == 'D') {
            $origen->tipo_recogida_id = TiposRecogidaBusiness::DOMICILIO;
            $origen->direccion = $envioReq['direccion_origen'];
        } else {
            return redirect()->back()->with(['error' => 'Datos incorrectos.']);
        }
        $origen->save();
        $envio->origen_id = $origen->id;

        // Pedido
        if (array_key_exists('referencia_pedido', $envioReq)) {
            $pedido = new PedidoBusiness();
            $pedido->configuracion_business_id = $usuario->configuracionBusiness->id;
            $pedido->num_pedido = $envioReq['referencia_pedido'];
            $pedido->save();
            $envio->pedido_id = $pedido->id;
        }

        // Paquete
        if ($envioReq['embalaje'] != 'Personalizado') {
            //TODO: comprobar que el embalaje existe en validacion
            $embalaje = PaqueteBusiness::where('nombre', $envioReq['embalaje'])->first();
            $envio->paquete_id = $embalaje->id;
        } else {
            $paquete = new PaqueteBusiness();
            $paquete->alto = $envioReq['alto'];
            $paquete->ancho = $envioReq['ancho'];
            $paquete->largo = $envioReq['largo'];
            $paquete->save();
            $envio->paquete_id = $paquete->id;
        }

        $envio->estado_id = Estado::VALIDADO;

        // Destinatario
        $destinatario = new DestinatarioBusiness();
        $destinatario->nombre = $envioReq['nombre_destinatario'];
        $destinatario->apellidos = $envioReq['apellido_destinatario'];
        $destinatario->email = $envioReq['email_destinatario'];
        $destinatario->pais_id = Pais::where('pref_tlf', substr($envioReq['telefono_destinatario'], 0, 2))->first()->id;
        $destinatario->telefono = substr($envioReq['telefono_destinatario'], 2);
        $destinatario->save();

        $envio->destinatario_id = $destinatario->id;

        // Destino
        $destino = new DestinoBusiness();
        $destino->pais_id = Pais::where('iso2', $envioReq['pais_destino'])->first()->id;
        $destino->cp_id = CodigoPostal::where('codigo_postal', $envioReq['cp_destino'])->whereHas('pais', function ($query) {
            $query->whereNotNull('zona_id');
        })->first()->id;

        if ($envioReq['tipo_entrega'] == 'S') {
            $destino->tipo_entrega_id = TiposRecogidaBusiness::STORE;
            $tipoStore = substr($envioReq['store_destino'], 0, 1);
            if ($tipoStore == Config::get('enums.tiposStores.puntoPack')) {
                $idStore = substr($envioReq['store_destino'], 1);
            } elseif ($tipoStore == Config::get('enums.tiposStores.transporter')) {
                $idStore = substr($envioReq['store_destino'], sizeof($envioReq['store_destino']) - 2);
            } else {
                return redirect()->back()->with(['error' => 'Datos incorrectos.']);
            }
            $destino->tipo_store_id = $tipoStore;
            $destino->store_id = $idStore;
        } elseif ($envioReq['tipo_entrega'] == 'D') {
            $destino->tipo_entrega_id = TiposRecogidaBusiness::DOMICILIO;
            $destino->direccion = $envioReq['direccion_destino'];
        } else {
            return redirect()->back()->with(['error' => 'Datos incorrectos.']);
        }

        $destino->save();
        $envio->destino_id = $destino->id;

        $envio->save();

        // Productos
        foreach ($envioReq['productos'] as $productoReq) {
            $rel = new ProductoEnvioBusiness();
            $producto = ProductoBusiness::where('nombre', ' - ', $productoReq['producto'])->first();
            if (!$producto) {
                $producto = new ProductoBusiness();
                $producto->nombre = $productoReq['producto'];
                $producto->peso = $productoReq['peso'];
                $producto->reembolsable = true;
                $producto->save();
            }
            $rel->producto_id = $producto->id;
            $rel->envio_id = $envio->id;
            $rel->cantidad = $productoReq['cantidad'];
            $rel->save();
        }

        $envio->precio = $this->calcularPrecio($envio->id);
        $envio->impuestos = number_format($envio->precio * $this->opciones->getImpuestos() / 100, 2);

        $envio->save();
    }

    public function generarDevolucion($devolucion)
    {
        $now = \Carbon::now();
        // Creamos envío de devolución y asignamos a la devolución
        if ($devolucion->envioDevolucion) {
            $envioDevolucion = $devolucion->envioDevolucion;
        } else {
            $envioDevolucion = $this->createEnvioDevolucion($devolucion);
        }
        $envioDevolucion->created_at = $now;
        $envioDevolucion->fecha_pago = $now;
        $devolucion->envio_devolucion_id = $envioDevolucion->id;
        if (!$envioDevolucion->envioMondialRelay && !$envioDevolucion->etiqueta_dual_carrier) {
            if ($envioDevolucion->origen->codigoPostal->codigo_pais == 'DE') {
                $this->mondialRelayService->crearDevolucionDualCarrier($envioDevolucion);
            } else {
                $this->mondialRelayService->crearDevolucion($devolucion, $envioDevolucion);
            }
        }

        $devolucion->created_at = $now;
        $devolucion->pagado = 1;
        $devolucion->finalizado = 1;
        $devolucion->save();

        $peso = 0;
        foreach ($devolucion->motivosDevolucionProductos as $motivo) {
            $peso += $motivo->producto->peso;
        }

        $envioDevolucion->precio = $this->calcularPrecioDevolucion($peso, $devolucion->envio->id, TipoDevolucionBusiness::DEVOLUCION);
        $envioDevolucion->impuestos = number_format($envioDevolucion->precio * $this->opciones->getImpuestos() / 100, 2);
        $envioDevolucion->save();

        if ($devolucion->envio->coste_cliente_devolucion) {
            $this->facturaService->createFacturaDevolucion($devolucion, $envioDevolucion);
            $peso = 0;
            foreach ($devolucion->motivosDevolucionProductos as $motivo) {
                $peso += $motivo->producto->peso;
            }

            $devolucion->peso = $peso;
            $devolucion->rango = RangoBusiness::where([['min', '<', $peso], ['max', '>=', $peso]])->first();

            $devolucion->precio = $this->calcularPrecioDevolucion($peso, $devolucion->envio_id, TipoDevolucionBusiness::DEVOLUCION);
        }
        if (!$devolucion->envio->etiqueta_preimpresa && ($devolucion->envio->destino->codigoPostal->codigo_pais == 'DE' || $devolucion->envio->destino->codigoPostal->codigo_pais == 'AT') && !$devolucion->envio->destino->mondialRelayStore) {
            $this->mondialRelayService->crearDevolucionDualCarrier($envioDevolucion);
        }

        $this->mailService->confirmarDevolucion($devolucion);
    }

    public function generarRetorno($envio)
    {

        $now = \Carbon::now();

        $envioRetorno = $envio->replicate();
        // Cambiamos origen y destino
        if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
            $oldOrigen = $envio->preferenciaRecogida;
        } else {
            $oldOrigen = $envio->origen;
        }
        $envioRetorno->tipo_origen_id = TipoOrigenBusiness::NUEVO;
        $origen = new OrigenBusiness();
        $origen->cp_id = $envio->destino->cp_id;
        $origen->tipo_recogida_id = $envio->destino->tipo_entrega_id;
        $origen->tipo_store_id = $envio->destino->tipo_store_id;
        $origen->store_id = $envio->destino->store_id;
        $origen->direccion = $envio->destino->direccion;
        $origen->save();
        $envioRetorno->origen_id = $origen->id;
        $destino = new DestinoBusiness();
        $destino->pais_id = $oldOrigen->codigoPostal->pais->id;
        $destino->cp_id = $oldOrigen->cp_id;
        $destino->tipo_entrega_id = $oldOrigen->tipo_recogida_id;
        $destino->tipo_store_id = $oldOrigen->tipo_store_id;
        $destino->store_id = $oldOrigen->store_id;
        $destino->direccion = $oldOrigen->direccion;
        $destino->save();
        $envioRetorno->destino_id = $destino->id;
        // Seteamos fechas a null
        $envioRetorno->fecha_pago = $now;
        $envioRetorno->fecha_origen = $now;
        $envioRetorno->fecha_ruta = $now;
        $envioRetorno->fecha_destino = null;
        $envioRetorno->fecha_finalizacion = null;
        // Seteamos ajustes de devolucion
        $envioRetorno->etiqueta_preimpresa = 0;
        $envioRetorno->coste_cliente_devolucion = 0;
        $envioRetorno->etiqueta_dual_carrier = 0;
        // Seteamos estado y codigo
        $envioRetorno->codigo = Uuid::generate()->string;
        $envioRetorno->estado_id = Estado::RUTA;

        $peso = 0;
        foreach ($envio->productos as $producto) {
            $peso += $producto->peso * $producto->pivot->cantidad;
        }
        $envioRetorno->precio = $this->calcularPrecioDevolucion($peso, $envio->id, TipoDevolucionBusiness::RETORNO);
        $envioRetorno->impuestos = number_format($envioRetorno->precio * $this->opciones->getImpuestos() / 100, 2);

        $envioRetorno->save();

        $devolucion = new DevolucionBusiness();
        $devolucion->tipo_devolucion_id = TipoDevolucionBusiness::RETORNO;
        $devolucion->envio_id = $envio->id;
        $devolucion->envio_devolucion_id = $envioRetorno->id;
        $devolucion->finalizado = 1;
        $devolucion->save();

        // Cambiamos localizador de envio original
        $envio->localizador = $envio->localizador . '(D)';
        $envio->save();

        $this->mailService->cobroDevolucion($devolucion);

    }

    public function createEnvioDevolucion($devolucion)
    {

        $envioOriginal = $devolucion->envio;

        $ecommerce = $devolucion->envio->configuracionBusiness;

        $envio = new EnvioBusiness();
        $envio->configuracion_business_id = $ecommerce->id;
        $envio->codigo = Uuid::generate()->string;
        $envio->tipo_origen_id = TipoOrigenBusiness::NUEVO;
        $origen = new OrigenBusiness();
        $storeOrigen = null;
        if ($devolucion->store_id) {
            $origen->tipo_recogida_id = TiposRecogidaBusiness::STORE;
            if ($devolucion->mondialRelayStore) {
                $storeOrigen = $devolucion->mondialRelayStore;
                $origen->tipo_store_id = Config::get('enums.tiposStores.puntoPack');
            } elseif ($devolucion->store) {
                $storeOrigen = $devolucion->store;
                $origen->tipo_store_id = Config::get('enums.tiposStores.transporter');
            }
            $origen->cp_id = $storeOrigen->codigoPostal->id;
            $origen->store_id = $storeOrigen->id;
        } else {
            $origen->cp_id = $envioOriginal->destino->cp_id;
            $origen->tipo_recogida_id = $envioOriginal->destino->tipo_entrega_id;
            if ($envioOriginal->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                $origen->direccion = $envioOriginal->destino->direccion;
            } else {
                $origen->store_id = $envioOriginal->destino->store_id;
                if ($envioOriginal->destino->mondialRelayStore) {
                    $origen->tipo_store_id = Config::get('enums.tiposStores.puntoPack');
                } else {
                    $origen->tipo_store_id = Config::get('enums.tiposStores.transporter');
                }
            }
        }
        $origen->save();
        $envio->origen_id = $origen->id;

        $envio->paquete_id = $envioOriginal->paquete_id;

        $envio->estado_id = Estado::PAGADO;
        $envio->fecha_pago = \Carbon::now();

        $envio->destinatario_id = $envioOriginal->destinatario->id;


        $destino = new DestinoBusiness();
        if ($envioOriginal->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
            $preferencia = $envioOriginal->preferenciaRecogida;
            $destino->pais_id = $preferencia->codigoPostal->pais->id;
            $destino->cp_id = $preferencia->codigoPostal->id;
            $destino->tipo_entrega_id = $preferencia->tipo_recogida_id;
            if ($destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                $destino->direccion = $preferencia->direccion;
            } else {
                $destino->tipo_store_id = $preferencia->tipo_store_id;
                $destino->store_id = $preferencia->store_id;
                if ($destino->tipo_store_id == Config::get('enums.tiposStores.puntoPack')) {
                    $destino->cp_id = $preferencia->mondialRelayStore->cp_id;
                } else {
                    $destino->cp_id = $preferencia->store->codigo_postal;
                }
            }
        } else {
            $destino->pais_id = $envioOriginal->origen->codigoPostal->pais->id;
            $destino->cp_id = $envioOriginal->origen->codigoPostal->id;
            $destino->tipo_entrega_id = $envioOriginal->origen->tipo_recogida_id;
            if ($destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                $destino->direccion = $envioOriginal->origen->direccion;
            } else {
                $destino->tipo_store_id = $envioOriginal->origen->tipo_store_id;
                $destino->store_id = $envioOriginal->origen->store_id;
                if ($destino->tipo_store_id == Config::get('enums.tiposStores.puntoPack')) {
                    $destino->cp_id = $envioOriginal->origen->mondialRelayStore->cp_id;
                } else {
                    $destino->cp_id = $envioOriginal->origen->store->codigo_postal;
                }
            }
        }
        $destino->save();
        $envio->destino_id = $destino->id;

        $envio->save();

//        for($i = 0 ; $i < count($request->nombre_producto) ; $i++) {
//            $rel = new ProductoEnvioBusiness();
//            if(strpos($request->nombre_producto[$i], '-') !== false) {
//                $nombre = trim(explode(' - ', $request->nombre_producto[$i])[1]);
//            } else {
//                $nombre = trim($request->nombre_producto[$i]);
//            }
//            $producto = ProductoBusiness::where('nombre', $nombre)->first();
//            if(!$producto) {
//                $producto = new ProductoBusiness();
//                $producto->nombre = $request->nombre_producto[$i];
//                $producto->peso = $request->peso_producto[$i];
//                $producto->reembolsable = true;
//                $producto->save();
//            }
//            $rel->producto_id = $producto->id;
//            $rel->envio_id = $envio->id;
//            $rel->cantidad = $request->num_productos[$i];
//            $rel->save();
//        }
        if ($devolucion->finalizado) {
            $peso = 0;
            foreach ($devolucion->motivosDevolucionProductos as $motivo) {
                $peso += $motivo->producto->peso;
            }

            $envio->precio = $this->calcularPrecioDevolucion($peso, $envio->id, TipoDevolucionBusiness::DEVOLUCION);
            $envio->impuestos = number_format($envio->precio * $this->opciones->getImpuestos() / 100, 2);
        }
        $envio->save();

        return $envio;
    }

    public function generarDevolucionPreimpresa($envio)
    {

        $ecommerce = $envio->configuracionBusiness;

        $devolucion = new DevolucionBusiness();

        $devolucion->tipo_devolucion_id = TipoDevolucionBusiness::DEVOLUCION;

        $devolucion->envio_id = $envio->id;

        if ($ecommerce->ajustesDevolucion->opcion_coste_id == OpcionCosteDevolucionBusiness::PREPAGADO) {
            $devolucion->pagado = 1;
        } else {
            $devolucion->pagado = 0;
        }

        $devolucion->finalizado = 0;

        $devolucion->save();

        $envioDevolucion = $this->createEnvioDevolucion($devolucion);

        $devolucion->envio_devolucion_id = $envioDevolucion->id;

        $devolucion->save();

        if ($envio->destino->codigoPostal->codigo_pais == 'DE' || $envio->destino->codigoPostal->codigo_pais == 'AT') {
            $this->mondialRelayService->crearDevolucionDualCarrier($envioDevolucion);
        } else {
            $this->mondialRelayService->crearDevolucion($devolucion, $envioDevolucion);
        }

    }

    public function searchPendientesPago($usuario, $text)
    {
        return $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::VALIDADO])
            ->whereDoesntHave('devolucionAsDevolucion')
            ->where(function ($query) use ($text) {
                $query->where('created_at', 'like', '%' . $text . '%');
                $query->orWhere(function ($query) use ($text) {
                    // Si es origen nuevo
                    $query->where('tipo_origen_id', TipoOrigenBusiness::NUEVO);
                    $query->whereHas('origen', function ($query) use ($text) {
                        $query->where(function ($query) use ($text) {
                            // Si es domicilio
                            $query->where('tipo_recogida_id', TiposRecogidaBusiness::DOMICILIO);
                            $query->where(function ($query) use ($text) {
                                $query->where('direccion', 'like', '%' . $text . '%');
                                $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                    $query->where('codigo_postal', 'like', '%' . $text . '%');
                                    $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                });
                            });
                        });
                        $query->orWhere(function ($query) use ($text) {
                            // Si es store
                            $query->where('tipo_recogida_id', TiposRecogidaBusiness::STORE);
                            $query->where(function ($query) use ($text) {
                                $query->whereHas('store', function ($query) use ($text) {
                                    // Si es store transporter
                                    $query->where('nombre', 'like', '%' . $text . '%');
                                    $query->orWhere('direccion', 'like', '%' . $text . '%');
                                    $query->orWhere('codigo_postal', 'like', '%' . $text . '%');
                                    $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                        $query->where('codigo_postal', 'like', '%' . $text . '%');
                                        $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                    });
                                });

                                $query->orWhereHas('mondialRelayStore', function ($query) use ($text) {
                                    // Si es store MondialRelay
                                    $query->where('nombre', 'like', '%' . $text . '%');
                                    $query->orWhere('direccion', 'like', '%' . $text . '%');
                                    $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                        $query->where('codigo_postal', 'like', '%' . $text . '%');
                                        $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                    });
                                });
                            });
                        });
                    });
                });
                $query->orWhere(function ($query) use ($text) {
                    // Si origen es preferenccia
                    $query->where('tipo_origen_id', TipoOrigenBusiness::PREFERENCIA);
                    $query->whereHas('configuracionBusiness.preferenciaRecogida', function ($query) use ($text) {
                        $query->where(function ($query) use ($text) {
                            // Si es domicilio
                            $query->where('tipo_recogida_id', TiposRecogidaBusiness::DOMICILIO);
                            $query->where(function ($query) use ($text) {
                                $query->where('direccion', 'like', '%' . $text . '%');
                                $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                    $query->where('codigo_postal', 'like', '%' . $text . '%');
                                    $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                });
                            });
                        });
                        $query->orWhere(function ($query) use ($text) {
                            // Si es store
                            $query->where('tipo_recogida_id', TiposRecogidaBusiness::STORE);
                            $query->where(function ($query) use ($text) {
                                $query->whereHas('store', function ($query) use ($text) {
                                    // Si es store transporter
                                    $query->where('nombre', 'like', '%' . $text . '%');
                                    $query->orWhere('direccion', 'like', '%' . $text . '%');
                                    $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                        $query->where('codigo_postal', 'like', '%' . $text . '%');
                                        $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                    });
                                });

                                $query->orWhereHas('mondialRelayStore', function ($query) use ($text) {
                                    // Si es store MondialRelay
                                    $query->where('nombre', 'like', '%' . $text . '%');
                                    $query->orWhere('direccion', 'like', '%' . $text . '%');
                                    $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                        $query->where('codigo_postal', 'like', '%' . $text . '%');
                                        $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                    });
                                });
                            });
                        });
                    });
                });
                $query->orWhereHas('pedido', function ($query) use ($text) {
                    $query->where('num_pedido', 'like', '%' . $text . '%');
                });
                $query->orWhereHas('productos', function ($query) use ($text) {
                    $query->where('referencia', 'like', '%' . $text . '%');
                    $query->orWhere('nombre', 'like', '%' . $text . '%');
                    $query->orWhere('peso', 'like', '%' . $text . '%');
                });
                $query->orWhereHas('paquete', function ($query) use ($text) {
                    $query->where('nombre', 'like', '%' . $text . '%');
                    $query->orWhere('largo', 'like', '%' . $text . '%');
                    $query->orWhere('alto', 'like', '%' . $text . '%');
                    $query->orWhere('ancho', 'like', '%' . $text . '%');
                });
                $query->orWhereHas('destinatario', function ($query) use ($text) {
                    $query->where('nombre', 'like', '%' . $text . '%');
                    $query->orWhere('apellidos', 'like', '%' . $text . '%');
                    $query->orWhere('email', 'like', '%' . $text . '%');
                    $query->orWhere('telefono', 'like', '%' . $text . '%');
                });
                $query->orWhereHas('destino', function ($query) use ($text) {
                    $query->where(function ($query) use ($text) {
                        // Si es domicilio
                        $query->where('tipo_entrega_id', TiposRecogidaBusiness::DOMICILIO);
                        $query->where(function ($query) use ($text) {
                            $query->where('direccion', 'like', '%' . $text . '%');
                            $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                $query->where('codigo_postal', 'like', '%' . $text . '%');
                                $query->orWhere('ciudad', 'like', '%' . $text . '%');
                            });
                        });
                    });
                    $query->orWhere(function ($query) use ($text) {
                        // Si es store
                        $query->where('tipo_entrega_id', TiposRecogidaBusiness::STORE);
                        $query->where(function ($query) use ($text) {
                            $query->whereHas('store', function ($query) use ($text) {
                                // Si es store transporter
                                $query->where('nombre', 'like', '%' . $text . '%');
                                $query->orWhere('direccion', 'like', '%' . $text . '%');
                                $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                    $query->where('codigo_postal', 'like', '%' . $text . '%');
                                    $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                });
                            });

                            $query->orWhereHas('mondialRelayStore', function ($query) use ($text) {
                                // Si es store MondialRelay
                                $query->where('nombre', 'like', '%' . $text . '%');
                                $query->orWhere('direccion', 'like', '%' . $text . '%');
                                $query->orWhereHas('codigoPostal', function ($query) use ($text) {
                                    $query->where('codigo_postal', 'like', '%' . $text . '%');
                                    $query->orWhere('ciudad', 'like', '%' . $text . '%');
                                });
                            });
                        });

                    });
                });
                $query->orWhereHas('estado', function ($query) use ($text) {
                    $query->where('nombre', 'like', '%' . $text . '%');
                });
            })
            ->orderBy('created_at', 'desc')->paginate(10);
    }

}
