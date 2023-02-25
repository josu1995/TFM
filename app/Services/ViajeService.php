<?php

namespace App\Services;

use App\Models\MetodoPago;
use App\Models\Punto;
use App\Models\Ruta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;

// Eventos
use Event;
use App\Events\CambiosEstado;

// Helpers
use App\Helpers\RedsysApi;

// Servicios
use App\Services\CalcularViaje;
use App\Services\MailService;
use Omnipay\Omnipay;

use Uuid;
use Config;
use Log;
use GuzzleHttp\Client;

// Modelos
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Viaje;
use App\Models\Usuario;
use App\Models\Metodo;
use App\Models\Posicion;
use App\Models\Pago;
use App\Models\Rol;

// Repositorios
use App\Repositories\OpcionRepository;

class ViajeService
{
    private $calcularViaje;
    private $mailService;
    private $opcionRepository;

    public function __construct(CalcularViaje $calcularViaje, MailService $mailService, OpcionRepository $opcionRepository)
    {
        $this->calcularViaje = $calcularViaje;
        $this->mailService = $mailService;
        $this->opcionRepository = $opcionRepository;
    }

    public function crearFormularioVerificacion() {

        $usuario = Auth::user();

        $order = strtotime('now');
        $identificador = Uuid::generate();

        if (Session::has('verify.viaje')) {
            $viaje = Viaje::find(Session::get('verify.viaje'));
        } else {
            $viaje = new Viaje();
        }

        $viaje->order_fianza = $order;
        $viaje->save();

        // Guardamos en session el order e identificador para tenerlos a mano durante el proceso
        Session::put('verify');
        Session::put('verify.order', $order);
        Session::put('verify.identificador', $identificador->string);
        Session::put('verify.viaje', $viaje->id);

        Log::info('Comienza la creación del proceso de fianza a TPV con order: '.$order.' e identificador de viaje: '.$identificador);

        $merchantTransactionType = "0";   // Código de transaction de pre-autorización

        $amount = '0.00';
        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantURL = route('fianza_success_tarjeta');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $redsys = new RedsysApi();
        $redsys->setParameter("DS_MERCHANT_AMOUNT", $amount * 100);
        $redsys->setParameter("DS_MERCHANT_ORDER", $order);
        $redsys->setParameter("DS_MERCHANT_MERCHANTCODE", $merchantCode);
        $redsys->setParameter("DS_MERCHANT_MERCHANTNAME", $merchantName);
        $redsys->setParameter("DS_MERCHANT_CURRENCY", $merchantCurrency);
        $redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $merchantTransactionType);
        $redsys->setParameter("DS_MERCHANT_TERMINAL", $merchantTerminal);
        $redsys->setParameter("DS_MERCHANT_MERCHANTURL", $merchantURL);
        $redsys->setParameter("DS_MERCHANT_URLOK", route('resumen_pago_viaje'));
        $redsys->setParameter("DS_MERCHANT_URLKO", route('resumen_pago_viaje'));
        $redsys->setParameter("DS_MERCHANT_TITULAR", $usuario->configuracion->nombre.' '.$usuario->configuracion->apellidos);
        $redsys->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION", 'Validación de tarjeta');
        $redsys->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $merchantLanguage);
        $redsys->setParameter("DS_MERCHANT_PAYMETHOD", 'C');
        $redsys->setParameter("DS_MERCHANT_IDENTIFIER", 'REQUIRED');

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        if (boolval($test) == true) {
            $url = env('REDSYS_URL_TEST');
        } else  {
            $url = env('REDSYS_URL_PRO');
        }

        //Datos de configuración
        $version="HMAC_SHA256_V1";
        $parametros = $redsys->createMerchantParameters();
        $signature = $redsys->createMerchantSignature($key);

        // Guardamos el método de pago
        $metodo = new MetodoPago();
        $metodo->usuario_id = $usuario->id;
        $metodo->ds_order = $order;
        $metodo->titular = $usuario->configuracion->nombre . ' ' . $usuario->configuracion->apellidos;
        $metodo->created_at = Carbon::now();
        $metodo->updated_at = Carbon::now();
        $metodo->activo = 0;
        $metodo->save();

        return [
            'version' => $version,
            'parametros' => $parametros,
            'signature' => $signature,
            'url' => $url
        ];
    }

    public function redirectFormularioVerificacion() {

        $usuario = Auth::user();
        // Parámetros petición
        $order = strtotime('now');

        $identificador = Uuid::generate();

        if (Session::has('verify.viaje')) {
            $viaje = Viaje::find(Session::get('verify.viaje'));
        } else {
            $viaje = new Viaje();
        }

        $viaje->order_fianza = $order;
        $viaje->save();

        Session::put('verify');
        Session::put('verify.order', $order);
        Session::put('verify.identificador', $identificador->string);
        Session::put('verify.viaje', $viaje->id);
        Session::save();

        $merchantTransactionType = "0";   // Código de transaction de autorización

        $amount = '0.00';

        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantURL = route('viaje_success_tarjeta');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        $referencia = 'REQUIRED';
        $directPayment = 'false';


        $params = array(
            'testMode' => $test,
            'amount' => 0.00 * 100,
            'description' => 'Validación de tarjeta',
            'consumerLanguage' => $merchantLanguage,
            'merchantName' => $merchantName,
            'titular' => $usuario->configuracion->nombre.' '.$usuario->configuracion->apellidos,

            'order' => $order,
            'token' => $order,

            'currency' => $merchantCurrency,
            'terminal' => $merchantTerminal,
            'transactionType' => $merchantTransactionType,

            'cancelUrl' => route('resumen_pago_viaje'),
            'returnUrl' => route('resumen_pago_viaje'),
            'merchantUrl' => route('viaje_success_tarjeta'),

            'merchantCode' => $merchantCode,
            'merchantKey' => $key,

            'identifier' => $referencia,

            'directPayment' => $directPayment,

            'signatureMode' => 'simple'
        );

        $gateway = Omnipay::create('Sermepa');
        $purchase = $gateway->purchase($params);
        $response = $purchase->send();

        $datos = $response->getData();

        $order_redsys_id = $order;

        if ($response->isRedirect()) {

            // Guardamos el método de pago
            $metodo = new MetodoPago();
            $metodo->usuario_id = $usuario->id;
            $metodo->ds_order = $order;
            $metodo->titular = $usuario->configuracion->nombre . ' ' . $usuario->configuracion->apellidos;
            $metodo->created_at = Carbon::now();
            $metodo->updated_at = Carbon::now();
            $metodo->activo = 0;
            $metodo->save();

            $response->redirect();
        }
    }

    // Función de método de borrar fianza
    public function deleteFianza()
    {
        $order = Session::get('fianza.order');

        Log::info('Comienzo de solicitar baja de preautorización: '.$order);

        $merchantTransactionType = "9";   // Código de transaction de anulación de pre-autorización

        $amount = $this->opcionRepository->getFianza();
        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantURL = route('fianza_delete_tarjeta');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $redsys = new RedsysApi();
        $redsys->setParameter("DS_MERCHANT_AMOUNT", $amount * 100);
        $redsys->setParameter("DS_MERCHANT_ORDER", $order);
        $redsys->setParameter("DS_MERCHANT_MERCHANTCODE", $merchantCode);
        $redsys->setParameter("DS_MERCHANT_CURRENCY", $merchantCurrency);
        $redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $merchantTransactionType);
        $redsys->setParameter("DS_MERCHANT_TERMINAL", $merchantTerminal);
        $redsys->setParameter("DS_MERCHANT_MERCHANTURL", $merchantURL);
        $redsys->setParameter("DS_MERCHANT_URLOK", route('resumen_pago_viaje'));
        $redsys->setParameter("DS_MERCHANT_URLKO", route('resumen_pago_viaje'));

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        if ($test) {
            $url = env('REDSYS_URL_TEST');
        } else  {
            $url = env('REDSYS_URL_PRO');
        }

        //Datos de configuración
        $version="HMAC_SHA256_V1";
        $parametros = $redsys->createMerchantParameters();
        $signature = $redsys->createMerchantSignature($key);

        return [
            'url' => $url,
            'Ds_SignatureVersion' => $version,
            'Ds_MerchantParameters' => $parametros,
            'Ds_Signature' => $signature
        ];
    }

    public function crearViaje(Usuario $usuario, Metodo $metodo)
    {
        $precioBase = $this->calcularViaje->calcularViaje();
        $gastosGestion = $this->calcularViaje->calcularGastosGestion();
        $precioFinal = $precioBase - $gastosGestion;
        $estado = Estado::find(Estado::SELECCIONADO);

        // Iniciamos transación
        DB::beginTransaction();

        $viaje = null;

        try {
            if (Session::has('verify.viaje')) {
                $viaje = Viaje::find(Session::get('verify.viaje'));
            } else {
                $viaje = new Viaje();
            }
            $identificador = Uuid::generate();

            $viaje->codigo = $identificador->string;
            $viaje->base = $precioBase;
            $viaje->impuestos = $this->calcularViaje->calcularIVA();
            $viaje->gestion = $gastosGestion - $viaje->impuestos;

            $viaje->transportista()->associate($usuario);

            $viaje->metodoCobro()->associate($metodo);
            $viaje->save();

            Log::info('Inicio de creación definitiva de viaje: '.$viaje->codigo.', order de fianza: '.$viaje->order_fianza);

            foreach (Session::get('envios_seleccionados') as $key => $envio) {
                $envioDB = Envio::find($envio['envio']->id);

                // Si hay algún envío que no se puede seleccionar (no está en entrega o intermedio),
                if($envioDB->estado_id != Estado::ENTREGA && $envioDB->estado_id != Estado::INTERMEDIO) {
                    throw new \Exception("Envíos no válidos para selección");
                }

                $envioDB->estado()->associate($estado);
                $envioDB->save();
                $envioDB->viajes()->attach($viaje->id);

                // Asociamos los envios con el viaje y lanzamos eventos de notificaciones
                Event::fire(new CambiosEstado($envioDB, $estado));

                if($envioDB->puntoEntrega->id != $envio['inicio']->id && $envioDB->puntoEntrega->localidad_id != $envio['inicio']->localidad_id) {
                    $posicion = new Posicion();
                    $posicion->viaje()->associate($viaje);
                    $posicion->puntoOrigen()->associate($envio['inicio']);
                    $posicion->envio()->associate($envio['envio']);
                    $posicion->save();
                }

                // Creamos posición intermedia si el punto de fin no coincide con el del envío
                if($envioDB->puntoRecogida->id != $envio['fin']->id && $envioDB->puntoRecogida->localidad_id != $envio['fin']->localidad_id) {
                    $posicion = new Posicion();
                    $posicion->viaje()->associate($viaje);
                    $posicion->puntoDestino()->associate($envio['fin']);
                    $posicion->envio()->associate($envio['envio']);
                    $posicion->save();
                }
            }

            Log::info('Se va a crear un pago relacionado con la fianza el viaje: '.$viaje->codigo.' para el usuario: '.$usuario->id);
            $pago = new Pago();
            $pago->transportista()->associate($usuario);
            $pago->viaje()->associate($viaje);
            $pago->valor = $precioFinal;
            $pago->save();
            Log::info('Pago almacenado');

        } catch (\Exception $e) {
            // Si se lanzó excepción, rollback
            Log::alert('No se ha podido crear un transporte: '.$e->getMessage());

            DB::rollback();
            return false;
        }

        // Si todo fue bien, realizamos operación en DB
        DB::commit();

        // Envío de mail a transportista
        $this->mailService->viaje($viaje);

        return $viaje;
    }

    public function crearViajePorEnvioLocalidadesYPuntos(Usuario $usuario, $iban, $envios, $localidadOrigen, $localidadDestino, $origenes, $destinos)
    {
        $precioBase = $this->calcularViaje->calcularViaje($envios, $localidadOrigen->id, $localidadDestino->id);
        $gastosGestion = $this->calcularViaje->calcularGastosGestion($envios, $localidadOrigen->id, $localidadDestino->id);
        $precioFinal = $precioBase - $gastosGestion;
        $estado = Estado::find(Estado::SELECCIONADO);

        // Iniciamos transación
        DB::beginTransaction();

        $viaje = null;

        try {
            $viaje = new Viaje();
            $identificador = Uuid::generate();

            $viaje->codigo = $identificador->string;
            $viaje->base = $precioBase;
            $viaje->impuestos = $this->calcularViaje->calcularIVA($envios, $localidadOrigen->id, $localidadDestino->id);
            $viaje->gestion = $gastosGestion - $viaje->impuestos;

            $viaje->transportista()->associate($usuario);

            $viaje->ccc = $iban;
            $viaje->save();

            Log::info('Inicio de creación definitiva de viaje: '.$viaje->codigo.', order de fianza: '.$viaje->order_fianza);

            $localidades = Ruta::where('localidad_inicio_id', $localidadOrigen->id)
                ->where('localidad_intermedia_id', $localidadDestino->id)
                ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

            $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

            $arrayDestinos = [];
            foreach ($envios as $envio) {
                if(in_array($envio->punto_recogida_id, $destinos)) {
                    if(array_key_exists($envio->punto_recogida_id, $arrayDestinos)) {
                        $arrayDestinos[$envio->punto_recogida_id]++;
                    } else {
                        $arrayDestinos[$envio->punto_recogida_id] = 1;
                    }
                }
            }


            $destinoIntermedio = null;
            if(!$arrayDestinos) {
                $destinoIntermedio = $destinos[0];
            } else {
                $destinoIntermedio = array_keys($arrayDestinos, max($arrayDestinos))[0];
            }


            foreach ($envios as $key => $envioDB) {

                // Si hay algún envío que no se puede seleccionar (no está en entrega o intermedio),
                if($envioDB->estado_id != Estado::ENTREGA && $envioDB->estado_id != Estado::INTERMEDIO) {
                    throw new \Exception("Envíos no válidos para selección");
                }

                $envioDB->estado()->associate($estado);
                $envioDB->save();
                $envioDB->viajes()->attach($viaje->id);

                // Asociamos los envios con el viaje y lanzamos eventos de notificaciones
                Event::fire(new CambiosEstado($envioDB, $estado));

                if($envioDB->puntoEntrega->localidad->id != $localidadOrigen->id) {
                    $posicion = new Posicion();
                    $posicion->viaje()->associate($viaje);
                    $posicion->puntoOrigen()->associate($envioDB->posicionesSinCancelar()->whereNotNull('punto_destino_id')->first()->punto_destino_id);
                    $posicion->envio()->associate($envioDB);
                    $posicion->save();
                }

                // Creamos posición intermedia si el punto de fin no coincide con el del envío
                if($envioDB->puntoRecogida->localidad->id != $localidadDestino->id) {
                    $posicion = new Posicion();
                    $posicion->viaje()->associate($viaje);
                    $posicion->punto_destino_id = $destinoIntermedio;
                    $posicion->envio()->associate($envioDB);
                    $posicion->save();
                }
            }

            Log::info('Se va a crear un pago relacionado con la fianza el viaje: '.$viaje->codigo.' para el usuario: '.$usuario->id);
            $pago = new Pago();
            $pago->transportista()->associate($usuario);
            $pago->viaje()->associate($viaje);
            $pago->valor = $precioFinal;
            $pago->save();
            Log::info('Pago almacenado');

        } catch (\Exception $e) {
            // Si se lanzó excepción, rollback
            Log::alert('No se ha podido crear un transporte: '.$e->getMessage());

            DB::rollback();
            return false;
        }

        // Si todo fue bien, realizamos operación en DB
        DB::commit();

        // Envío de mail a transportista
        $this->mailService->viajeProfesional($viaje, $origenes, $destinos);

        return $viaje;
    }
}
