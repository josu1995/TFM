<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Punto;
use App\Models\Embalaje;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Models\OrigenBusiness;
use App\Models\PaqueteBusiness;
use App\Models\PedidoBusiness;
use App\Models\ProductoBusiness;
use App\Models\ProductoEnvioBusiness;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use App\Repositories\BusinessRepository;
use App\Services\Business\EnvioService;
use App\Services\MessageService;
use App\Services\MondialRelayService;
use App\Services\TarjetaService;
use Auth;
use Config;
use Illuminate\Http\Request;
use Validator;
use Geocoder;
use App;
use View;
use Excel;
use App\Exports\EnviosPendientesExpedicionBusinessExport;
use App\Services\Business\TrackingService;
use App\Services\Business\MailService;

class EnvioController extends Controller
{

    private $businessRepository;
    private $mondialRelayService;
    protected $messageService;
    protected $envioService;
    protected $tarjetaService;
    protected $trackingService;
    protected $opciones;
    protected $mailService;

    /**
     * Create a new controller instance.
     */
    public function __construct(MailService $mailService, BusinessRepository $businessRepository, MondialRelayService $mondialRelayService, MessageService $messageService, EnvioService $envioService, TarjetaService $tarjetaService, TrackingService $trackingService, App\Repositories\OpcionRepository $opciones)
    {
        $this->middleware('auth.business');
        $this->businessRepository = $businessRepository;
        $this->mondialRelayService = $mondialRelayService;
        $this->messageService = $messageService;
        $this->envioService = $envioService;
        $this->tarjetaService = $tarjetaService;
        $this->trackingService = $trackingService;
        $this->opciones = $opciones;
        $this->mailService = $mailService;
    }

    public function getPendientesPago(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->ajax()) {
            return view('business.home.envios.pendientesPagoTable', [
              
            ]);
        } else {
            return view('business.home.envios.pendientesPago', [
               
            ]);
        }
    }

    public function editOrigen(Request $request, $id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = EnvioBusiness::find($id);

        $validator = Validator::make($request->all(), [
            'cp_origen_id' => 'required|numeric|exists:codigos_postales,id',
            'tipo_recogida_id' => 'required|numeric|exists:tipos_recogida_business,id',
            'direccion_edit' => 'required_if:tipo_recogida_edit,' . TiposRecogidaBusiness::DOMICILIO . '|max:500',
            'punto_origen_id' => 'required_if:tipo_recogida_edit,' . TiposRecogidaBusiness::STORE . '|numeric|store_exists:origen',
            'punto_origen_tipo' => 'required_if:tipo_recogida_edit,' . TiposRecogidaBusiness::STORE . ',punto_origen_id|exists:tipos_recogida_business,id',
        ], [
            'cp_origen_id.required' => 'Es necesario seleccionar un código postal del desplegable.',
            'cp_origen_id.numeric' => 'El código postal no tiene un formato correcto.',
            'cp_origen_id.exists' => 'El código postal introducido no existe en el sistema.',
            'tipo_recogida_id.required' => 'Es necesario seleccionar un tipo de recogida del desplegable.',
            'tipo_recogida_id.numeric' => 'El tipo de recogida no tiene un formato correcto.',
            'tipo_recogida_id.exists' => 'El tipo de recogida introducido no existe en el sistema.',
            'direccion_edit.required_if' => 'Es necesario introducir una dirección de recogida.',
            'direccion_edit.max' => 'La dirección de recogida no puede superar los 500 caracteres.',
            'punto_origen_id.required_if' => 'Es necesario seleccionar un store de recogida.',
            'punto_origen_id.numeric' => 'El store de recogida no tiene un formato correcto.',
            'punto_origen_id.store_exists' => 'El store de recogida no existe en el sistema.',
            'punto_origen_tipo.required_if' => 'Es necesario seleccionar un store de recogida.',
            'punto_origen_tipo.numeric' => 'El store de recogida no tiene un formato correcto.',
            'punto_origen_tipo.store_exists' => 'El store de recogida no existe en el sistema.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'origenEdit')->withInput()->with(['error_id' => $envio->id]);
        }

        if(!$request->store_origen_id) {
            // Actualizamos origen
            $origen = new OrigenBusiness();
            $origen->cp_id = $request->cp_origen_id;
            $origen->tipo_recogida_id = $request->tipo_recogida_id;
            if ($origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origen->tipo_store_id = null;
                $origen->store_id = null;
                $origen->direccion = $request->direccion_edit;
            } else {
                $origen->direccion = null;
                $origen->tipo_store_id = $request->punto_origen_tipo;
                $origen->store_id = substr($request->punto_origen_id, 1);
            }

            $origen->save();

            $envio->tipo_origen_id = TipoOrigenBusiness::NUEVO;
            $envio->origen_id = $origen->id;
            $envio->save();
        }

        $this->messageService->flashMessage('Origen actualizado correctamente');
        return redirect()->route('business_envios_pendientes_pago');
    }

    public function editPedido(Request $request, $id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = EnvioBusiness::find($id);

        $pedidoId = $envio->pedido ? $envio->pedido->id : 'null';

        $peso_array = array();
        foreach ($request->peso_producto_edit as $peso) {
            array_push($peso_array, str_replace(',', '.', $peso));
        }
        $request['peso_producto_edit'] = $peso_array;

        $validator = Validator::make($request->all(), [
            'referencia_edit' => 'sometimes|nullable|max:255|unique:pedidos_business,num_pedido,' . $pedidoId . ',id,configuracion_business_id,' . $usuario->configuracionBusiness->id,
            'nombre_producto_edit.*' => 'required|max:255',
            'num_productos_edit.*' => 'required|numeric|min:1',
            'peso_producto_edit.*' => 'bail|required|regex:/^[0-9]\d*(\.\d+)?$/i|min:0.01|sum_pesos_min:0,num_productos_edit|sum_pesos:0,num_productos_edit',
        ], [
            'referencia_edit.max' => 'El número de referencia no puede contener más de 255 caracteres.',
            'referencia_edit.unique' => 'El número de pedido introducido ha sido usado anteriormente.',
            'nombre_producto_edit.*.required' => 'El nombre de producto es obligatorio.',
            'nombre_producto_edit.*.max' => 'El nombre de producto no puede contener más de 255 caracteres.',
            'num_productos_edit.*.required' => 'El número de productos es obligatorio.',
            'num_productos_edit.*.numeric' => 'El número de productos debe ser numérico.',
            'num_productos_edit.*.min' => 'El número de productos debe ser mayor que 0.',
            'peso_producto_edit.*.required' => 'El peso de producto es obligatorio.',
            'peso_producto_edit.*.regex' => 'El peso de producto debe ser numérico.',
            'peso_producto_edit.*.min' => 'El peso de producto debe ser mayor que 0.',
            'peso_producto_edit.*.sum_pesos_min' => 'La suma de pesos de los productos debe ser mayor que 0.10Kg.',
            'peso_producto_edit.*.sum_pesos' => 'La suma de pesos de los productos no puede superar los 20kg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'pedidoEdit')->withInput()->with(['error_id' => $envio->id]);
        }

        // Actualizamos pedido
        if ($request->referencia_edit) {
            $pedido = $envio->pedido;
            if (!$pedido) {
                $pedido = new PedidoBusiness();
            }
            $pedido->configuracion_business_id = $usuario->configuracionBusiness->id;
            $pedido->num_pedido = $request->referencia_edit;
            $pedido->save();
            $envio->pedido_id = $pedido->id;
            $envio->save();
        } elseif ($envio->pedido) {
            $envio->pedido()->dissociate();
        }

        // Actualizamos productos
        foreach ($envio->productos as $producto) {
            $envio->productos()->detach($producto->id);
        }
        for ($i = 0; $i < count($request->nombre_producto_edit); $i++) {
            $rel = new ProductoEnvioBusiness();
            if (strpos($request->nombre_producto[$i], '-') !== false && ProductoBusiness::where([['configuracion_business_id', $usuario->configuracionBusiness->id], ['referencia', trim(explode(' - ', $request->nombre_producto[$i])[0])]])->count()) {
                $nombre = trim(explode(' - ', $request->nombre_producto_edit[$i])[1]);
            } else {
                $nombre = trim($request->nombre_producto_edit[$i]);
            }
            $producto = ProductoBusiness::where([['nombre', $nombre], ['configuracion_business_id', $usuario->configuracionBusiness->id]])->first();

            $newProducto = new ProductoBusiness();
            if ($producto) {
                $newProducto->nombre = $producto->nombre;
            } else {
                $newProducto->nombre = $request->nombre_producto_edit[$i];
            }
            $newProducto->peso = $request->peso_producto_edit[$i];
            $newProducto->reembolsable = true;

            if (!$producto || $producto->peso != $newProducto->peso) {
                $newProducto->save();
                $rel->producto_id = $newProducto->id;
            } else {
                $rel->producto_id = $producto->id;
            }
            $rel->envio_id = $envio->id;
            $rel->cantidad = $request->num_productos_edit[$i];
            $rel->save();
        }

        $envio->precio = $this->envioService->calcularPrecio($envio->id);
        $envio->impuestos = number_format($envio->precio * $this->opciones->getImpuestos() / 100, 2);
        $envio->save();

        $this->messageService->flashMessage('Pedido actualizado correctamente');
        return redirect()->route('business_envios_pendientes_pago');
    }

    public function editPaquete(Request $request, $id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = EnvioBusiness::find($id);

        if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
            $origen = $envio->preferenciaRecogida;
        } else {
            $origen = $envio->origen;
        }

        $sumMedidasValidation = $origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO && $envio->destino->codigoPostal->codigo_pais == 'ES' ? 'sum_medidas_domicilio_edit' : 'sum_medidas_store_edit';

        if ($origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO && $envio->destino->codigoPostal->codigo_pais == 'ES') {
            $maxMedidasValidation = '';
        } else {
            $maxMedidasValidation = '|max_medidas_edit';
        }

        $validator = Validator::make($request->all(), [
            'embalaje_edit' => 'required|numeric|embalaje_business',
            'alto' => 'required_if:embalaje,0|numeric|' . $sumMedidasValidation . $maxMedidasValidation,
            'ancho' => 'required_if:embalaje,0|numeric' . $maxMedidasValidation,
            'largo' => 'required_if:embalaje,0|numeric' . $maxMedidasValidation,
        ], [
            'embalaje_edit.required' => 'El embalaje es obligatorio.',
            'embalaje_edit.numeric' => 'El embalaje debe ser numérico.',
            'embalaje_edit.embalaje_business' => 'El embalaje seleccionado no existe entre tus embalajes.',
            'alto.required_if' => 'El alto del embalaje es obligatorio.',
            'alto.numeric' => 'El alto del embalaje debe ser numérico.',
            'alto.sum_medidas_store_edit' => 'La suma de las medidas del paquete no pueden superar los 150cm',
            'alto.sum_medidas_domicilio_edit' => 'La suma de las medidas del paquete no pueden superar los 250cm',
            'alto.max_medidas_edit' => 'El alto no puede superar los 120cm.',
            'ancho.required_if' => 'El ancho del embalaje es obligatorio.',
            'ancho.numeric' => 'El ancho del embalaje debe ser numérico.',
            'ancho.max_medidas_edit' => 'El ancho no puede superar los 120cm.',
            'largo.required_if' => 'El largo del embalaje es obligatorio.',
            'largo.numeric' => 'El largo del embalaje debe ser numérico.',
            'largo.max_medidas_edit' => 'El largo no puede superar los 120cm.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'paqueteEdit')->withInput()->with(['error_id' => $envio->id]);
        }

        // Actualizamos paquete
        if ($request->embalaje_edit == 0 && !$envio->paquete->configuracion_business_id) {
            $paquete = $envio->paquete;
            $paquete->largo = $request->largo;
            $paquete->alto = $request->alto;
            $paquete->ancho = $request->ancho;
            $paquete->save();
        } elseif ($request->embalaje_edit == 0 && $envio->paquete->configuracion_business_id) {
            $paquete = new PaqueteBusiness();
            $paquete->largo = $request->largo;
            $paquete->alto = $request->alto;
            $paquete->ancho = $request->ancho;
            $paquete->save();
            $envio->paquete_id = $paquete->id;
        } elseif ($request->embalaje_edit != 0 && !$envio->paquete->configuracion_business_id) {
            $envio->paquete()->delete();
            $envio->paquete_id = $request->embalaje_edit;
        } else {
            $envio->paquete_id = $request->embalaje_edit;
        }
        $envio->save();

        $this->messageService->flashMessage('Paquete actualizado correctamente');
        return redirect()->back();
    }

    public function editDestinatario(Request $request, $id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = EnvioBusiness::find($id);

        $validator = Validator::make($request->all(), [
            'nombre_edit' => 'required|min:2|max:30',
            'apellidos_edit' => 'required|min:2|max:30',
            'email_edit' => 'required|email|min:7|max:70',
            'prefijo' => 'required|exists:paises,id',
            'telefono_edit' => 'required|numeric|business_phone:prefijo',
            'pais_destino_id' => 'required|numeric|exists:paises,id',
            'cp_destino_id' => 'required|numeric|exists:codigos_postales,id',
            'tipo_entrega_destino_id' => 'required|numeric|exists:tipos_recogida_business,id',
            'direccion_destino_edit' => 'sometimes|nullable|required_if:tipo_entrega_destino_id,' . TiposRecogidaBusiness::DOMICILIO . '|min:2|max:64',
            'punto_destino_id' => 'nullable|required_if:tipo_entrega_destino_id,' . TiposRecogidaBusiness::STORE . '|numeric|store_exists:destino,pais_destino_id',
            'punto_destino_tipo' => 'required_if:tipo_entrega_destino_id,' . TiposRecogidaBusiness::STORE . '|numeric',
        ], [
            'nombre_edit.required' => 'El nombre del destinatario es obligatorio.',
            'nombre_edit.min' => 'El nombre del destinatario debe tener como mínimo 2 caracteres.',
            'nombre_edit.max' => 'El nombre del destinatario debe tener como máximo 32 caracteres.',
            'apellidos_edit.required' => 'Los apellidos del destinatario son obligatorios.',
            'apellidos_edit.min' => 'Los apellidos del destinatario deben tener 2 caracteres como mínimo.',
            'apellidos_edit.max' => 'Los apellidos del destinatario deben tener 32 caracteres como máximo.',
            'email_edit.required' => 'El email del destinatario es obligatorio.',
            'email_edit.email' => 'El email del destinatario no tiene un formato correcto.',
            'email_edit.min' => 'El email del destinatario debe tener 7 caracteres como mínimo.',
            'email_edit.max' => 'El email del destinatario debe tener 70 caracteres como máximo.',
            'prefijo.required' => 'El prefijo telefónico es obligatorio.',
            'prefijo.exists' => 'El prefijo indicado no es correcto.',
            'telefono_edit.required' => 'El teléfono del destinatario es obligatorio.',
            'telefono_edit.numeric' => 'El teléfono del destinatario no tiene un formato correcto.',
            'telefono_edit.business_phone' => 'El teléfono del destinatario no tiene un formato correcto para el país indicado.',
            'pais_destino_id.required' => 'El país de destino es obligatorio.',
            'pais_destino_id.numeric' => 'Es necesario seleccionar un país de destino del dedsplegable.',
            'pais_destino_id.exists' => 'El país de destino seleccionado no existe en el sistema.',
            'cp_destino_id.required' => 'El código postal de destino es obligatorio.',
            'cp_destino_id.numeric' => 'Es necesario seleccionar un código postal de destino del dedsplegable.',
            'cp_destino_id.exists' => 'El código postal de destino seleccionado no existe en el sistema.',
            'tipo_entrega_destino_id.required' => 'El tipo de entrega en destino es obligatorio.',
            'tipo_entrega_destino_id.numeric' => 'Es necesario seleccionar un tipo de entrega en destino del dedsplegable.',
            'tipo_entrega_destino_id.exists' => 'El tipo de entrega en destino seleccionado no existe en el sistema.',
            'direccion_destino_edit.required_if' => 'La dirección de destino es obligatoria.',
            'direccion_destino_edit.min' => 'La dirección de destino debe ser mayor que 2 caracteres.',
            'direccion_destino_edit.max' => 'La dirección de destino debe ser menor que 64 caracteres.',
            'punto_destino_id.required_if' => 'El store de destino es obligatorio.',
            'punto_destino_id.store_exists' => 'El store de destino no existe en el sistema.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'destinatarioEdit')->withInput()->with(['error_id' => $envio->id]);
        }

        // Actualizamos destinatario
        $destinatario = $envio->destinatario;
        $destinatario->nombre = $request->nombre_edit;
        $destinatario->apellidos = $request->apellidos_edit;
        $destinatario->email = $request->email_edit;
        $destinatario->telefono = $request->telefono_edit;
        $destinatario->save();

        // Actualizamos destino
        $destino = $envio->destino;
        $destino->pais_id = $request->pais_destino_id;
        $destino->cp_id = $request->cp_destino_id;
        $destino->tipo_entrega_id = $request->tipo_entrega_destino_id;
        if ($destino->tipo_entrega_id == TiposRecogidaBusiness::STORE) {
            $destino->tipo_store_id = $request->punto_destino_tipo;
            $destino->store_id = substr($request->punto_destino_id, 1);
        } else {
            $destino->direccion = $request->direccion_destino_edit;
        }
        $destino->save();

        $envio->precio = $this->envioService->calcularPrecio($envio->id);
        $envio->impuestos = number_format($envio->precio * $this->opciones->getImpuestos() / 100, 2);

        $envio->save();

        $this->messageService->flashMessage('Destinatario actualizado correctamente');
        return redirect()->route('business_envios_pendientes_pago');
    }

    public function eliminarEnvios(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->ids) {

            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $request->ids)->get();

            foreach ($envios as $envio) {
                $envio->delete();
            }

            if (count($envios) > 1) {
                $this->messageService->flashMessage('Envíos eliminados correctamente.');
            } else {
                $this->messageService->flashMessage('Envío eliminado correctamente.');
            }
            return redirect()->route('business_envios_pendientes_pago');
        } else {
            $this->messageService->flashMessage('Primero debes seleccionar un envío');
            return redirect()->back();
        }
    }

    public function pagarEnvios(Request $request)
    {
        $usuario = Auth::guard('business')->user();

        if ($request->ids) {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $request->ids)->get();
            return $this->tarjetaService->pagarEnviosBusiness($envios);
        } else {
            $this->messageService->flashMessage('Primero debes seleccionar un envío');
            return redirect()->back();
        }
    }

    // PENDIENTES EXPEDICION
    public function getPendientesExpedicion(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->t) {
            $text = $request->t;
            $envios = $usuario->configuracionBusiness->envios()->where('fecha_pago', 'like', '%' . $text . '%')->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_pago', 'desc')->paginate(10);
        } else {
            $envios = $usuario->configuracionBusiness->envios()->where('estado_id', Estado::PAGADO)->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_pago', 'desc')->paginate(10);
        }

        $embalajes = $usuario->configuracionBusiness->paquetes;

        if ($request->ajax()) {
            return view('business.home.envios.pendientesExpedicionTable', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        } else {

            if (count($usuario->configuracionBusiness->erroresMondialRelay()->where('leido', 0)->get())) {
                $message = 'No hemos podido generar tus etiquetas debido a un elevado número de solicitudes. En unos momentos las tendrás disponibles.';
                $usuario->configuracionBusiness->erroresMondialRelay()->where('leido', 0)->update(['leido' => 1]);
            } else {
                $message = \Session::get('message');
            }

            return view('business.home.envios.pendientesExpedicion', [
                'envios' => $envios,
                'embalajes' => $embalajes,
                'message' => $message
            ]);
        }
    }

    public function showOrigen($id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = $usuario->configuracionBusiness->envios()->where('id', $id)->first();

        $result = [];

        if (!$envio->devolucionAsDevolucion) {

            if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                $preferencia = $envio->preferenciaRecogida;
                if ($preferencia->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                    if ($preferencia->mondialRelayStore) {
                        $result['nombre'] = $preferencia->mondialRelayStore->nombre;
                        $result['ciudad'] = $preferencia->codigoPostal->ciudad;
                        $result['calle'] = $preferencia->mondialRelayStore->direccion;
                        $result['latitud'] = $preferencia->mondialRelayStore->latitud;
                        $result['longitud'] = $preferencia->mondialRelayStore->longitud;
                        $result['imagen'] = $preferencia->mondialRelayStore->imagen ? $preferencia->mondialRelayStore->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $preferencia->mondialRelayStore->horarios;
                    } else {
                        $result['nombre'] = $preferencia->store->nombre;
                        $result['ciudad'] = $preferencia->codigoPostal->ciudad;
                        $result['calle'] = $preferencia->store->direccion;
                        $result['latitud'] = $preferencia->store->latitud;
                        $result['longitud'] = $preferencia->store->longitud;
                        $result['imagen'] = $preferencia->store->imagen ? $preferencia->store->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $envio->preferencia->store->horarios;
                    }
                } else {
                    $result['nombre'] = $preferencia->direccion;
                    $result['ciudad'] = $preferencia->codigoPostal->ciudad;
                    $address = Geocoder::geocode($preferencia->direccion . ' ' . $preferencia->codigoPostal->codigo_postal . ' ' . $preferencia->codigoPostal->ciudad . ' ' . $preferencia->codigoPostal->codigo_pais)->get()->first();
                    $result['latitud'] = $address->getCoordinates()->getLatitude();
                    $result['longitud'] = $address->getCoordinates()->getLongitude();
                }
            } elseif ($envio->tipo_origen_id == TipoOrigenBusiness::NUEVO) {
                if ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                    if ($envio->origen->mondialRelayStore) {
                        $result['nombre'] = $envio->origen->mondialRelayStore->nombre;
                        $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                        $result['calle'] = $envio->origen->mondialRelayStore->direccion;
                        $result['latitud'] = $envio->origen->mondialRelayStore->latitud;
                        $result['longitud'] = $envio->origen->mondialRelayStore->longitud;
                        $result['imagen'] = $envio->origen->mondialRelayStore->imagen ? $envio->origen->mondialRelayStore->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $envio->origen->mondialRelayStore->horarios;
                    } else {
                        $result['nombre'] = $envio->origen->store->nombre;
                        $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                        $result['calle'] = $envio->origen->store->direccion;
                        $result['latitud'] = $envio->origen->store->latitud;
                        $result['longitud'] = $envio->origen->store->longitud;
                        $result['imagen'] = $envio->origen->store->imagen ? $envio->origen->store->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $envio->origen->store->horarios;
                    }
                } else {
                    $result['nombre'] = $envio->origen->direccion;
                    $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                    $address = Geocoder::geocode($envio->origen->direccion . ' ' . $envio->origen->codigoPostal->codigo_postal . ' ' . $envio->origen->codigoPostal->ciudad . ' ' . $envio->origen->codigoPostal->codigo_pais)->get()->first();
                    $result['latitud'] = $address->getCoordinates()->getLatitude();
                    $result['longitud'] = $address->getCoordinates()->getLongitude();
                }
            }
        } else {
            $devolucion = $envio->devolucionAsDevolucion;
            if ($devolucion->tipo_devolucion == App\Models\TipoDevolucionBusiness::DEVOLUCION) {
                $result['nombre'] = $devolucion->mondialRelayStore->nombre;
                $result['ciudad'] = $devolucion->mondialRelayStore->codigoPostal->ciudad;
                $result['calle'] = $devolucion->mondialRelayStore->direccion;
                $result['latitud'] = $devolucion->mondialRelayStore->latitud;
                $result['longitud'] = $devolucion->mondialRelayStore->longitud;
                $result['imagen'] = $devolucion->mondialRelayStore->imagen ? $devolucion->mondialRelayStore->imagen : asset('img/home/store-no-img.png');
                $result['horarios'] = $devolucion->mondialRelayStore->horarios;
            } else {
                $envio = $devolucion->envioDevolucion;
                if ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                    if ($envio->origen->mondialRelayStore) {
                        $result['nombre'] = $envio->origen->mondialRelayStore->nombre;
                        $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                        $result['calle'] = $envio->origen->mondialRelayStore->direccion;
                        $result['latitud'] = $envio->origen->mondialRelayStore->latitud;
                        $result['longitud'] = $envio->origen->mondialRelayStore->longitud;
                        $result['imagen'] = $envio->origen->mondialRelayStore->imagen ? $envio->origen->mondialRelayStore->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $envio->origen->mondialRelayStore->horarios;
                    } else {
                        $result['nombre'] = $envio->origen->store->nombre;
                        $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                        $result['calle'] = $envio->origen->store->direccion;
                        $result['latitud'] = $envio->origen->store->latitud;
                        $result['longitud'] = $envio->origen->store->longitud;
                        $result['imagen'] = $envio->origen->store->imagen ? $envio->origen->store->imagen : asset('img/home/store-no-img.png');
                        $result['horarios'] = $envio->origen->store->horarios;
                    }
                } else {
                    $result['nombre'] = $envio->origen->direccion;
                    $result['ciudad'] = $envio->origen->codigoPostal->ciudad;
                    $address = Geocoder::geocode($envio->origen->direccion . ' ' . $envio->origen->codigoPostal->codigo_postal . ' ' . $envio->origen->codigoPostal->ciudad . ' ' . $envio->origen->codigoPostal->codigo_pais)->get()->first();
                    $result['latitud'] = $address->getCoordinates()->getLatitude();
                    $result['longitud'] = $address->getCoordinates()->getLongitude();
                }
            }

        }

        return response()->json($result);

    }

    public function showDestino($id)
    {

        $usuario = Auth::guard('business')->user();

        $envio = $usuario->configuracionBusiness->envios()->where('id', $id)->first();

        $result = [];

        if ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $result['nombre'] = $envio->destino->direccion;
            $result['ciudad'] = $envio->destino->codigoPostal->ciudad;
            $address = Geocoder::geocode($envio->destino->direccion . ' ' . $envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
            $result['latitud'] = $address->getCoordinates()->getLatitude();
            $result['longitud'] = $address->getCoordinates()->getLongitude();
        } else {
            if ($envio->destino->mondialRelayStore) {
                $result['nombre'] = $envio->destino->mondialRelayStore->nombre;
                $result['ciudad'] = $envio->destino->codigoPostal->ciudad;
                $result['calle'] = $envio->destino->mondialRelayStore->direccion;
                $result['latitud'] = $envio->destino->mondialRelayStore->latitud;
                $result['longitud'] = $envio->destino->mondialRelayStore->longitud;
                $result['imagen'] = $envio->destino->mondialRelayStore->imagen ? $envio->destino->mondialRelayStore->imagen : asset('img/home/store-no-img.png');
                $result['horarios'] = $envio->destino->mondialRelayStore->horarios;
            } else {
                $result['nombre'] = $envio->destino->store->nombre;
                $result['ciudad'] = $envio->destino->codigoPostal->ciudad;
                $result['calle'] = $envio->destino->store->direccion;
                $result['latitud'] = $envio->destino->store->latitud;
                $result['longitud'] = $envio->destino->store->longitud;
                $result['imagen'] = $envio->destino->store->imagen ? $envio->destino->store->imagen : asset('img/home/store-no-img.png');
                $result['horarios'] = $envio->destino->store->horarios;
            }
        }

        return response()->json($result);

    }

    public function postSeleccion(Request $request)
    {
        $request->session()->flash('selectedEnvios', $request->data);
        return response()->json();
    }

    public function exportarPdf(Request $request)
    {

        $selected = $request->session()->get('selectedEnvios');

        $usuario = Auth::guard('business')->user();

        $pdf = App::make('dompdf.wrapper');

        $pdf->setPaper(array(0, 0, 595, 842), 'landscape');

        $envios = null;

        if ($selected) {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $selected)->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_pago')->get();
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::PAGADO])->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_pago')->get();
        }

        $nombre_comercial = $usuario->configuracionBusiness->nombre_comercial;

        $view = View::make('business.home.envios.pdfPendientesExpedicionExport', compact('envios', 'nombre_comercial', 'usuario'))->render();
        $pdf->loadHTML($view);

        return $pdf->stream('Envios_pendientes_expedición_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.pdf');
    }

    public function exportarXls(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $selected = $request->session()->get('selectedEnvios');

        return Excel::download(new EnviosPendientesExpedicionBusinessExport($usuario, $selected), 'Envios_pendientes_expedición_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.xlsx');
    }

    public function cancelarPendientesExpedicion(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $selected = $request->session()->get('selectedEnvios');

        $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $selected)->whereDoesntHave('devolucionAsDevolucion')->get();

        $now = \Carbon::now();

        foreach ($envios as $envio) {
            $envio->estado_id = Estado::CANCELADO;
            $envio->fecha_finalizacion = $now;
            $envio->save();
            $this->mailService->cancelacionEnvio($envio);
        }

        $this->messageService->flashMessage('Envíos cancelados correctamente');
        return redirect()->route('business_envios_pendientes_expedicion');
    }

    // EN TRANSITO
    public function getEnTransito(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->t) {
            $text = $request->t;
            $envios = $usuario->configuracionBusiness->envios()->where('fecha_origen', 'like', '%' . $text . '%')->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_origen')->paginate(10);
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::ENTREGA, Estado::RUTA, Estado::INTERMEDIO])->whereDoesntHave('devolucionAsDevolucion')->paginate(10);
        }

        if ($request->ajax()) {
            return view('business.home.envios.enTransitoTable', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        } else {

            return view('business.home.envios.enTransito', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        }
    }

    public function showSeguimiento($id, Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $envio = $usuario->configuracionBusiness->envios()->where([['id', $id], ['estado_id', '>=', Estado::PAGADO]])->first();

        if (!$envio) {
            return redirect()->back(400);
        }

        $this->trackingService->actualizarEstados($envio);

        $markers = $this->trackingService->getMarkersInfo($envio);

        $historicoEstados = $this->trackingService->getInfoEstados($envio);

        $estado = $this->trackingService->getEstadoActual($envio);

        $proximoEstado = $this->trackingService->getProximoEstado($envio);

        return view('business.home.envios.partials.modals.showTracking', ['envio' => $envio, 'markers' => json_encode($markers), 'estados' => $historicoEstados, 'estadoActual' => $estado, 'proximoEstado' => $proximoEstado]);

    }

    public function exportarPdfTransito(Request $request)
    {

        $selected = $request->session()->get('selectedEnvios');

        $usuario = Auth::guard('business')->user();

        $pdf = App::make('dompdf.wrapper');

        $envios = null;

        if ($selected) {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $selected)->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_origen')->get();
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::ENTREGA, Estado::RUTA, Estado::INTERMEDIO])->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_origen')->get();
        }

        $nombre_comercial = $usuario->configuracionBusiness->nombre_comercial;

        $view = View::make('business.home.envios.pdfEnTransitoExport', compact('envios', 'nombre_comercial', 'usuario'))->render();
        $pdf->loadHTML($view);

        return $pdf->stream('Envios_en_transito_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.pdf');
    }

    public function exportarXlsTransito(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $selected = $request->session()->get('selected');

        return Excel::download(new App\Exports\EnviosEnTransitoBusinessExport($usuario, $selected), 'Envios_en_transito_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.xlsx');
    }

    // EN DESTINO
    public function getDestino(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->t) {
            $text = $request->t;
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::RECOGIDA, Estado::REPARTO])->where('fecha_destino', 'like', '%' . $text . '%')->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_origen')->paginate(10);
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::RECOGIDA, Estado::REPARTO])->whereDoesntHave('devolucionAsDevolucion')->paginate(10);
        }

        if ($request->ajax()) {
            return view('business.home.envios.destinoTable', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        } else {

            return view('business.home.envios.destino', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        }
    }

    public function exportarPdfDestino(Request $request)
    {

        $selected = $request->session()->get('selectedEnvios');

        $usuario = Auth::guard('business')->user();

        $pdf = App::make('dompdf.wrapper');

        $envios = null;

        if ($selected) {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $selected)->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_destino')->get();
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::RECOGIDA])->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_destino')->get();
        }

        $nombre_comercial = $usuario->configuracionBusiness->nombre_comercial;

        $view = View::make('business.home.envios.pdfDestinoExport', compact('envios', 'nombre_comercial', 'usuario'))->render();
        $pdf->loadHTML($view);

        return $pdf->stream('Envios_en_destino_reparto_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.pdf');
    }

    public function exportarXlsDestino(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $selected = $request->session()->get('selected');

        return Excel::download(new App\Exports\EnviosDestinoExport($usuario, $selected), 'Envios_en_destino_reparto_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.xlsx');
    }

    // FINALIZADOS
    public function getFinalizados(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        if ($request->t) {
            $text = $request->t;
            $envios = $usuario->configuracionBusiness->envios()->where('fecha_finalizacion', 'like', '%' . $text . '%')->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_finalizacion', 'desc')->paginate(10);
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::FINALIZADO, Estado::CANCELADO, Estado::DEVUELTO])->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_finalizacion', 'desc')->paginate(10);
        }

        if ($request->ajax()) {
            return view('business.home.envios.finalizadosTable', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        } else {

            return view('business.home.envios.finalizados', [
                'envios' => $envios,
                'message' => \Session::get('message')
            ]);
        }
    }

    public function exportarPdfFinalizados(Request $request)
    {

        $selected = $request->session()->get('selectedEnvios');

        $usuario = Auth::guard('business')->user();

        $pdf = App::make('dompdf.wrapper');

        $pdf->setPaper(array(0, 0, 595, 842), 'landscape');

        $envios = null;

        if ($selected) {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('id', $selected)->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_finalizacion', 'desc')->get();
        } else {
            $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::FINALIZADO, Estado::CANCELADO, Estado::DEVUELTO])->whereDoesntHave('devolucionAsDevolucion')->orderBy('fecha_finalizacion', 'desc')->get();
        }

        $nombre_comercial = $usuario->configuracionBusiness->nombre_comercial;

        $view = View::make('business.home.envios.pdfFinalizadosExport', compact('envios', 'nombre_comercial', 'usuario'))->render();
        $pdf->loadHTML($view);

        return $pdf->stream('Envios_finalizados_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.pdf');
    }

    public function exportarXlsFinalizados(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $selected = $request->session()->get('selected');

        return Excel::download(new App\Exports\EnviosFinalizadosExport($usuario, $selected), 'Envios_finalizados_' . $usuario->configuracionBusiness->nombre_comercial . '_' . \Carbon::now()->format('dmY') . '.xlsx');
    }

    public function searchFinalizados(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $text = $request->t;

        $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::FINALIZADO, Estado::CANCELADO, Estado::DEVUELTO])
            ->whereDoesntHave('devolucionAsDevolucion')
            ->where(function ($query) use ($text) {
                $query->where('fecha_pago', 'like', '%' . $text . '%');
                $query->orWhere('fecha_origen', 'like', '%' . $text . '%');
                $query->orWhere('fecha_destino', 'like', '%' . $text . '%');
                $query->orWhere('localizador', 'like', '%' . $text . '%');
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
                            $query->where('direccion', 'like', '%' . $text . '%');
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
                    $query->orWhere('nombre_business', 'like', '%' . $text . '%');
                });
                $query->orWhere('fecha_finalizacion', 'like', '%' . $text . '%');
                $query->orwhere('localizador', 'like', '%' . $text . '%');
            })
            ->orderBy('fecha_finalizacion', 'desc')->paginate(10);

        return view('business.home.envios.finalizadosTable', ['envios' => $envios]);

    }

    public function searchDestino(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $text = $request->t;

        $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::RECOGIDA, Estado::REPARTO])
            ->whereDoesntHave('devolucionAsDevolucion')
            ->where(function ($query) use ($text) {
                $query->where('fecha_pago', 'like', '%' . $text . '%');
                $query->orWhere('fecha_origen', 'like', '%' . $text . '%');
                $query->orWhere('fecha_destino', 'like', '%' . $text . '%');
                $query->orWhere('localizador', 'like', '%' . $text . '%');
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
                            $query->where('direccion', 'like', '%' . $text . '%');
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
                    $query->orWhere('nombre_business', 'like', '%' . $text . '%');
                });
                $query->orwhere('localizador', 'like', '%' . $text . '%');
            })
            ->orderBy('fecha_destino', 'desc')->paginate(10);

        return view('business.home.envios.destinoTable', ['envios' => $envios]);

    }

    public function searchTransito(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $text = $request->t;

        $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::ENTREGA, Estado::RUTA, Estado::INTERMEDIO])
            ->whereDoesntHave('devolucionAsDevolucion')
            ->where(function ($query) use ($text) {
                $query->where('fecha_pago', 'like', '%' . $text . '%');
                $query->orWhere('fecha_origen', 'like', '%' . $text . '%');
                $query->orWhere('localizador', 'like', '%' . $text . '%');
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
                    // Si origen es preferencia
                    $query->where('tipo_origen_id', TipoOrigenBusiness::PREFERENCIA);
                    $query->whereHas('configuracionBusiness.preferenciaRecogida', function ($query) use ($text) {
                        $query->where(function ($query) use ($text) {
                            // Si es domicilio
                            $query->where('tipo_recogida_id', TiposRecogidaBusiness::DOMICILIO);
                            $query->where('direccion', 'like', '%' . $text . '%');
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
                    $query->orWhere('nombre_business', 'like', '%' . $text . '%');
                });
                $query->orwhere('localizador', 'like', '%' . $text . '%');
            })
            ->orderBy('fecha_origen', 'desc')->paginate(10);

        return view('business.home.envios.enTransitoTable', ['envios' => $envios]);

    }

    public function searchPendientesExpedicion(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $text = $request->t;

        $envios = $usuario->configuracionBusiness->envios()->whereIn('estado_id', [Estado::PAGADO])
            ->whereDoesntHave('devolucionAsDevolucion')
            ->where(function ($query) use ($text) {
                $query->where('fecha_pago', 'like', '%' . $text . '%');
                $query->orWhere('localizador', 'like', '%' . $text . '%');
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
            ->orderBy('fecha_pago', 'desc')->paginate(10);

        return view('business.home.envios.pendientesExpedicionTable', ['envios' => $envios]);

    }

    public function searchPendientesPago(Request $request)
    {

        $usuario = Auth::guard('business')->user();

        $text = $request->t;

        $envios = $this->envioService->searchPendientesPago($usuario, $text);

        return view('business.home.envios.pendientesPagoTable', ['envios' => $envios]);

    }

}
