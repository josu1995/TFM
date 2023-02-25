<?php

namespace App\Services;

// Eventos
use App\Models\MetodoPago;
use Carbon\Carbon;
use Event;
use App\Events\PedidoRealizado;
use Config;

// Utilidades
use Uuid;
use Omnipay\Omnipay;
use Auth;

// Modelos
use App\Models\Pedido;
use App\Models\Metodo;
use App\Models\Usuario;
use App\Models\Estado;
use App\Models\EstadosPago;

use Log;

class PagoService
{
    protected $calcularPrecio;
    protected $envioService;

    public function __construct(CalcularPrecio $calcularPrecio, EnvioService $envioService)
    {
        $this->calcularPrecio = $calcularPrecio;
        $this->envioService = $envioService;
    }

    // Proceso de pago
    // Recibe array de envíos
    // Recibe usuario que realiza el pago
    // Recibe Método de pago
    public function procesarPago($envios, Usuario $usuario, Metodo $metodo, $tarjeta = null)
    {
        $identificador = Uuid::generate();

        if($this->calcularPrecio->calcularPedido($envios) == 0) {
            // Si el envio es gratuito
            $this->crearPedidoGratuito($envios, $identificador->string, $usuario, $metodo);

        } else {

            if ($metodo->nombre == 'paypal') {
                // Parámetros petición
                $params = array(
                    'cancelUrl' => route('resumen_pagos'),
                    'returnUrl' => route('pago_success_paypal'),
                    'name' => $usuario->configuracion->nombre . ' ' . $usuario->configuracion->apellidos,
                    'description' => 'Pedido: ' . $identificador,
                    'amount' => $this->calcularPrecio->calcularPedido($envios),
                    'currency' => 'EUR'
                );

                $gateway = Omnipay::create('PayPal_Rest');

                //Sandbox
                $gateway->setClientId(env('PAYPAL_CLIENTID'));
                $gateway->setSecret(env('PAYPAL_SECRET'));
                $gateway->setTestMode(env('PAGO_TEST'));

                // Enviamos
                $response = $gateway->purchase($params)->send();

                // Si tuvo éxito en la petición
                if ($response->isSuccessful()) {
                    $datos = $response->getData();
                    $transaction_id = $datos['id'];

                    $estadoPago = EstadosPago::find(EstadosPago::PROCESO);

                    // Creamos pedido con estado en proceso
                    $pedido = $this->crearPedidoPaypal($envios, $usuario, $identificador, $metodo, $estadoPago, $transaction_id);

                } else {
                    return $response->getMessage();
                }

                // Si la respuesta es una redirección, redirige a route('pago_success_tarjeta') o cancel
                if ($response->isRedirect()) {
                    $response->redirect();
                }

            } else if ($metodo->nombre == 'tarjeta') {
                // Parámetros petición
                $amount = $this->calcularPrecio->calcularPedido($envios);
                $order = strtotime('now');

                $merchantCode = Config::get('redsys.defaults.merchant_code');
                $merchantCurrency = Config::get('redsys.defaults.currency');
                $merchantTransactionType = Config::get('redsys.defaults.transaction_type');
                $merchantURL = route('pago_success_tarjeta');
                $merchantTerminal = Config::get('redsys.defaults.terminal');
                $merchantName = Config::get('redsys.defaults.merchant_data');
                $merchantLanguage = Config::get('redsys.defaults.consumer_language');

                $key = env('REDSYS_MERCHANT_KEY');
                $test = Config::get('redsys.defaults.test_mode');

                $referencia = 'REQUIRED';
                $directPayment = 'false';
                if(!is_null($tarjeta) && $tarjeta >= 0) {
                    $referencia = MetodoPago::find($tarjeta)->referencia;
                    $directPayment = 'true';
                }


                $params = array(
                    'testMode' => $test,
                    'amount' => $amount * 100,
                    'description' => 'Pedido: ' . $identificador,
                    'consumerLanguage' => $merchantLanguage,
                    'merchantName' => $merchantName,
                    'titular' => $usuario->configuracion->nombre.' '.$usuario->configuracion->apellidos,

                    'order' => $order,
                    'token' => $order,

                    'currency' => $merchantCurrency,
                    'terminal' => $merchantTerminal,
                    'transactionType' => $merchantTransactionType,

                    'cancelUrl' => route('resumen_pagos'),
                    'returnUrl' => route('resumen_pedidos', ['finalizado'=>True]),
                    'merchantUrl' => route('pago_success_tarjeta'),

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
                $estadoPago = EstadosPago::find(EstadosPago::PROCESO);

                // Creamos pedido
                $pedido = $this->crearPedidoTarjeta($envios, $usuario, $identificador, $metodo, $estadoPago, $order_redsys_id);

                if ($response->isRedirect()) {

                    if($referencia == 'REQUIRED') {
                        $metodo = new MetodoPago();
                        $metodo->usuario_id = $usuario->id;
                        $metodo->ds_order = $order;
                        $metodo->titular = $usuario->configuracion->nombre . ' ' . $usuario->configuracion->apellidos;
                        $metodo->created_at = Carbon::now();
                        $metodo->updated_at = Carbon::now();
                        $metodo->activo = 0;
                        $metodo->save();
                    }

                    $response->redirect();
                }

            }
        }
    }

    // Proceso de pago satisfactorio
    public function getSuccessPayment(Pedido $pedido, Metodo $metodo, $transaction_id, $comprador_id)
    {
        if($metodo->nombre == 'paypal'){
            $params = array(
                'payerId'              => $comprador_id,
                'transactionReference' => $transaction_id,
                'cancelUrl' 	       => route('resumen_pagos'),
                'returnUrl' 	       => route('resumen_pedidos', ['finalizado' => True]),
                'name'	               => $pedido->usuario->configuracion->nombre.' '.$pedido->usuario->configuracion->apellidos,
                'description' 	       => 'Pedido: '.$pedido->identificador,
                'amount'               => $this->calcularPrecio->calcularPedido($pedido->envios),
                'currency' 	           => 'EUR'
            );

            $gateway = Omnipay::create('PayPal_Rest');
            $gateway->setClientId(env('PAYPAL_CLIENTID'));
            $gateway->setSecret(env('PAYPAL_SECRET'));
            $gateway->setTestMode(env('PAGO_TEST'));

            $response = $gateway->completePurchase($params)->send();
            $reference = $response->getTransactionReference();

            if ($response->isSuccessful()) {
                $estado = EstadosPago::find(EstadosPago::PAGADO);
                $pedido = $this->actualizarPedido($pedido, $estado, $comprador_id, $reference);

                // Evento de pedido realizado (lanza email con QR, etc.)
                Event::fire(new PedidoRealizado($pedido, $pedido->usuario));
            } else {
                return $response->getMessage();
            }

            if ($response->isRedirect()) {
                $response->redirect();
            }
        } else if($metodo->nombre == 'tarjeta'){

            $estado = EstadosPago::find(EstadosPago::PAGADO);
            $pedido = $this->actualizarPedido($pedido, $estado, null, null);

            Event::fire(new PedidoRealizado($pedido, $pedido->usuario));
        }
    }

    // Guardado de pedido en DB y actualización de estados/precio de los envíos
    private function crearPedidoPaypal($envios, Usuario $usuario, $uuid, Metodo $metodo, EstadosPago $estado, $transaction_id)
    {
        $pedido = new Pedido();
        $pedido->base = $this->calcularPrecio->calcularPrecioBase($envios);
        $pedido->embalajes = $this->calcularPrecio->calcularTotalEmbalajes($envios);
        $pedido->coberturas = $this->calcularPrecio->calcularTotalCoberturas($envios);
        $pedido->gestion = $this->calcularPrecio->calcularExtrasTotal($envios);
        $pedido->identificador = $uuid;
        $pedido->transaction_paypal_id = $transaction_id;
        $pedido->descuento = $this->calcularPrecio->calcularDescuentoPedido($envios);
        $pedido->metodo()->associate($metodo);
        $pedido->estado()->associate($estado);
        $pedido->usuario()->associate($usuario);
        $pedido->save();

        // Asociamos envíos a pedido, y le asignamos precio
        foreach ($envios as $envio) {
            $this->envioService->actualizacionPrecio($envio);
            $pedido->envios()->save($envio);
            $envio->pedido()->associate($pedido);
            $envio->save();
        }

        return $pedido;
    }

    // Guardado de pedido en DB y actualización de estados/precio delos envíos
    public function crearPedidoTarjeta($envios, Usuario $usuario, $uuid, Metodo $metodo, EstadosPago $estado, $order_redsys_id = null)
    {
        $pedido = new Pedido();
        $pedido->base = $this->calcularPrecio->calcularPrecioBase($envios);
        $pedido->embalajes = $this->calcularPrecio->calcularTotalEmbalajes($envios);
        $pedido->coberturas = $this->calcularPrecio->calcularTotalCoberturas($envios);
        $pedido->gestion = $this->calcularPrecio->calcularExtrasTotal($envios);
        $pedido->identificador = $uuid;
        $pedido->descuento = $this->calcularPrecio->calcularDescuentoPedido($envios);
        $pedido->order_redsys_id = $order_redsys_id;
        $pedido->metodo()->associate($metodo);
        $pedido->estado()->associate($estado);
        $pedido->usuario()->associate($usuario);
        $pedido->save();

        // Asociamos envíos a pedido, y le asignamos precio
        foreach ($envios as $envio) {
            $this->envioService->actualizacionPrecio($envio);
            $pedido->envios()->save($envio);
            $envio->pedido()->associate($pedido);
            $envio->created_at = Carbon::now();
            $envio->save();
        }

        return $pedido;
    }

    private function crearPedidoGratuito($envios, $uuid, Usuario $usuario, Metodo $metodo)
    {
        $pedido = new Pedido();
        $pedido->base = $this->calcularPrecio->calcularPrecioBase($envios);
        $pedido->embalajes = $this->calcularPrecio->calcularTotalEmbalajes($envios);
        $pedido->coberturas = $this->calcularPrecio->calcularTotalCoberturas($envios);
        $pedido->gestion = $this->calcularPrecio->calcularExtrasTotal($envios);
        $pedido->identificador = $uuid;
        $pedido->descuento = $this->calcularPrecio->calcularDescuentoPedido($envios);
        $pedido->metodo()->associate($metodo);
        $pedido->estado()->associate(EstadosPago::find(EstadosPago::PAGADO));
        $pedido->usuario()->associate($usuario);
        $pedido->save();

        // Asociamos envíos a pedido, y le asignamos precio
        foreach ($envios as $envio) {
            $this->envioService->actualizacionPrecio($envio);
            $pedido->envios()->save($envio);
            $envio->pedido()->associate($pedido);
            $envio->created_at = Carbon::now();
            $this->envioService->cambioEstado($envio, Estado::PAGADO);
            $envio->save();
        }

        Event::fire(new PedidoRealizado($pedido, $pedido->usuario));

        return $pedido;
    }

    // Actualizar pedido a estado pagado. Actualización de envíos asociados al pedido
    private function actualizarPedido(Pedido $pedido, EstadosPago $estado, $payer_id, $transaction_id)
    {
        $pedido->payer_paypal_id = $payer_id;
        $pedido->transaction_paypal_id = $transaction_id;
        $pedido->estado()->associate($estado);
        $pedido->save();

         // Actualizamos estados de los envíos a pagados
        foreach ($pedido->envios as $envio) {
            $this->envioService->cambioEstado($envio, Estado::PAGADO);
            $pedido->envios()->save($envio);
            $envio->pedido()->associate($pedido);
            $envio->save();
        }

       return $pedido;
    }
}
