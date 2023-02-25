<?php

namespace App\Services;

// Modelos
use App\Models\Usuario;
use Crypt;

class EnviarSMS {

    // Validación de teléfono de usuario. Devuelve errores o false si tuvo éxito
    public function validarTelefono($usuario)
    {
        $telefono = $usuario->configuracion->telefono;
        $key = env('SMS_KEY');
        $remite = env('SMS_REMITE');
        $codigo = explode('-', $usuario->identificador)[1];

        // Creamos el mensaje
        $request = '{
            "api_key":"'.$key.'",
            "messages":[
                {
                    "from":"'.$remite.'",
                    "to":"'.$telefono.'",
                    "text":"El código para confirmar tu móvil es '.$codigo.'"
                }
            ]
        }';

        // Enviamos
        $headers = array('Content-Type: application/json');

        $ch = curl_init('https://api.gateway360.com/api/3.0/sms/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        // Evitar error 60 de curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        // print_r($result);
        // print_r(curl_errno($ch));
        // die();

        // Si tiene errores, devolvemos el código, si no true
        if (curl_errno($ch) != 0 ){
        	// return curl_errno($ch);
            return $result;
        } else {
            return false;
        }
    }

    public function smsDevolucionBusiness($envio)
    {
        $telefono = $envio->destinatario->telefono;
        $key = env('SMS_KEY');
        $remite = $envio->configuracionBusiness->nombre_comercial;

        $ruta = route('business_devolucion', ['pedido_id' => $envio->codigo]);

        if($envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }

        $pedidoId = ' ';
        if($envio->pedido) {
            if($envio->destino->codigoPostal->codigo_pais != 'ES') {
                $pedidoId = strlen($envio->pedido->num_pedido) > 16 ? substr($envio->pedido->num_pedido, 0, 16) : $envio->pedido->num_pedido;
            } else {
                $pedidoId = strlen($envio->pedido->num_pedido) > 12 ? substr($envio->pedido->num_pedido, 0, 12) : $envio->pedido->num_pedido;
            }
        }

        // Creamos el mensaje
        $request = '{
            "api_key":"'.$key.'",
            "link":"' . $ruta . '",
            "messages":[
                {
                    "from":"'.substr($remite, 0, 11).'",
                    "to":"+'. $envio->destinatario->pais->pref_tlf . $telefono.'",
                    "text":"' . \Lang::get('sms.devolucion.confirmacion', ['localizador' => $pedidoId]) . '"
                }
            ]
        }';

        // Enviamos
        $headers = array('Content-Type: application/json');

        $ch = curl_init('https://api.gateway360.com/api/3.0/sms/send-link');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        // Evitar error 60 de curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        // print_r($result);
        // print_r(curl_errno($ch));
        // die();

        // Si tiene errores, devolvemos el código, si no true
        if (curl_errno($ch) != 0 ){
            // return curl_errno($ch);
            return $result;
        } else {
            return false;
        }
    }

    public function validateMobilePhone($envio) {
        $tlf = $envio->destinatario->telefono;

        switch ($envio->destinatario->pais->iso2) {
            case 'ES':
                return preg_match_all("/^[6|7][0-9]{8}$/", $tlf);
                break;
            case 'FR':
                return preg_match_all("/^[6|7][0-9]{8}$/", $tlf);
                break;
            case 'BE':
                return preg_match_all("/^[4]?[0-9]{8}$/", $tlf);
                break;
            case 'LU':
                return preg_match_all("/^[6][0-9]{8}$/", $tlf);
                break;
            case 'DE':
                return preg_match_all("/^(15|16|17)[0-9]{9}$/", $tlf);
                break;
        }

        return false;
    }
}
