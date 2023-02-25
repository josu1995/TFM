<?php

namespace App\Services;

// Repositorios
use App\Events\CambiosEstadoBusiness;
use App\Repositories\OpcionRepository;
use Event;
use App\Models\Envio;

use App\Models\Estado;
use App\Models\MetodoPago;
use Auth;
use Config;
use Omnipay\Omnipay;
use Carbon;

class TarjetaService
{

    protected $opciones;

    public function __construct(OpcionRepository $opciones) {
        $this->opciones = $opciones;
    }


        public function verificarTarjeta($usuario, $returnRoute) {

        $order = strtotime('now');

        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantTransactionType = Config::get('redsys.defaults.transaction_type');
        $merchantURL = route('verificar_tarjeta_success');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        $params = array(
            'testMode' => $test,
            'amount' => '0.00',
            'description' => 'Validación de tarjeta',
            'consumerLanguage' => $merchantLanguage,
            'merchantName' => $merchantName,
            'titular' => $merchantName,

            'order' => $order,
            'token' => $order,

            'currency' => $merchantCurrency,
            'terminal' => $merchantTerminal,
            'transactionType' => $merchantTransactionType,

            'cancelUrl' => route($returnRoute),
            'returnUrl' => route($returnRoute),
            'merchantUrl' => $merchantURL,

            'merchantCode' => $merchantCode,
            'merchantKey' => $key,

            'identifier' => 'REQUIRED',

            'signatureMode' => 'simple'
        );

        $gateway = Omnipay::create('Sermepa');
        $purchase = $gateway->purchase($params);
        $response = $purchase->send();

        if ($response->isRedirect()) {

            $metodo = new MetodoPago();
            $metodo->usuario_id = $usuario->id;
            $metodo->ds_order = $order;
            $metodo->titular = $usuario->configuracion->nombre . ' ' . $usuario->configuracion->apellidos;
            $metodo->created_at = Carbon::now();
            $metodo->updated_at = Carbon::now();
            $metodo->activo = 0;
            $metodo->save();

            $response->redirect();
        } else {
            return redirect()->route($returnRoute)->with(['error' => 'En este momento no hemos podido verificar tu tarjeta. Inténtalo de nuevo en unos minutos']);
        }
    }

    public function pagarEnviosBusiness($envios) {

        $usuario = Auth::guard('business')->user();

        $order = strtotime('now');

        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantTransactionType = Config::get('redsys.defaults.transaction_type');
        $merchantURL = route('pagar_envios_business_success');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        $metodoPago = $usuario->metodosPago()->where('activo', 1)->first();
        if($metodoPago && $metodoPago->referencia) {
            $referencia = $metodoPago->referencia;
            $directPayment = 'true';
        } else {
            $referencia = 'REQUIRED';
            $directPayment = 'false';
        }

        // Calculamos precio
        $precio = 0;
        foreach ($envios as $envio) {
            $precio += $envio->precio + number_format($envio->precio * $this->opciones->getImpuestos()/100, 2) ;
        }

        $params = array(
            'testMode' => $test,
            'amount' => $precio * 100,
            'description' => 'Pago',
            'consumerLanguage' => $merchantLanguage,
            'merchantName' => $merchantName,
            'titular' => $merchantName,

            'order' => $order,
            'token' => $order,

            'currency' => $merchantCurrency,
            'terminal' => $merchantTerminal,
            'transactionType' => $merchantTransactionType,

            'cancelUrl' => route('business_envios_pendientes_pago'),
            'returnUrl' => route('business_envios_pendientes_expedicion'),
            'merchantUrl' => $merchantURL,

            'merchantCode' => $merchantCode,
            'merchantKey' => $key,

            'identifier' => $referencia,

            'directPayment' => $directPayment,

            'signatureMode' => 'simple'
        );

        $gateway = Omnipay::create('Sermepa');
        $purchase = $gateway->purchase($params);
        $response = $purchase->send();

        if ($response->isRedirect()) {

            foreach ($envios as $envio) {
                $envio->order_redsys_id = $order;
                $envio->save();
            }

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

//            $headers = array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8');
//            $ch = curl_init($response->getRedirectUrl());
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, 'Ds_MerchantParameters='.$response->getData()['Ds_MerchantParameters'].'&Ds_Signature='.$response->getData()['Ds_Signature'].'&Ds_SignatureVersion='.$response->getData()['Ds_SignatureVersion']);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            // Evitar error 60 de curl
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//
//
//            $result = curl_exec($ch);

            // print_r($result);
            // print_r(curl_errno($ch));
            // die();

//            // Si tiene errores, devolvemos el código, si no true
//            if (curl_errno($ch) != 0 ){
//                // return curl_errno($ch);
//                return $result;
//            } else {
//                return false;
//            }



//            Event::fire(new CambiosEstadoBusiness($envios, Estado::find(Estado::PAGADO)));
//            return redirect()->route('business_envios_pendientes_pago');
            $response->redirect();
        } else {
            return redirect()->route('business_envios_pendientes_pago')->with(['error' => 'En este momento no hemos podido verificar tu tarjeta. Inténtalo de nuevo en unos minutos']);
        }
    }

    public function pagarDevolucionBusiness($devolucion, $precio) {

        $order = strtotime('now');

        $merchantCode = Config::get('redsys.defaults.merchant_code');
        $merchantCurrency = Config::get('redsys.defaults.currency');
        $merchantTransactionType = Config::get('redsys.defaults.transaction_type');
        $merchantURL = route('pagar_devolucion_business_success');
        $merchantTerminal = Config::get('redsys.defaults.terminal');
        $merchantName = Config::get('redsys.defaults.merchant_data');
        $merchantLanguage = Config::get('redsys.defaults.consumer_language');

        $key = env('REDSYS_MERCHANT_KEY');
        $test = Config::get('redsys.defaults.test_mode');

        $ecommerceCode = explode('-', $devolucion->envio->configuracionBusiness->usuario->identificador)[sizeof(explode('-', $devolucion->envio->configuracionBusiness->usuario->identificador)) - 1];

        $params = array(
            'testMode' => $test,
            'amount' => ($precio + number_format($precio * $this->opciones->getImpuestos()/100, 2)) * 100,
            'description' => 'Pago devolución',
            'consumerLanguage' => $merchantLanguage,
            'merchantName' => $merchantName,
            'titular' => $merchantName,

            'order' => $order,
            'token' => $order,

            'currency' => $merchantCurrency,
            'terminal' => $merchantTerminal,
            'transactionType' => $merchantTransactionType,

            'cancelUrl' => route('business_devolucion_confirmar', [
                'pedido_id' => $devolucion->envio->codigo,
            ]),
            'returnUrl' => route('business_devolucion_finalizar', [
                'pedido_id' => $devolucion->envio->codigo,
                'devolucion_id' => $devolucion->id
            ]),
            'merchantUrl' => $merchantURL,

            'merchantCode' => $merchantCode,
            'merchantKey' => $key,

//            'identifier' => 'REQUIRED',

            'signatureMode' => 'simple'
        );

        $gateway = Omnipay::create('Sermepa');
        $purchase = $gateway->purchase($params);
        $response = $purchase->send();

        if ($response->isRedirect()) {

            $devolucion->order_redsys_id = $order;
            $devolucion->save();

//            Event::fire(new CambiosEstadoBusiness($envios, Estado::find(Estado::PAGADO)));
//            return redirect()->route('business_envios_pendientes_pago');
            $response->redirect();
        } else {
            return redirect()->route('business_envios_pendientes_pago')->with(['error' => 'En este momento no hemos podido verificar tu tarjeta. Inténtalo de nuevo en unos minutos']);
        }
    }
}
