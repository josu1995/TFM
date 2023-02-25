<?php

namespace App\Services\Business;

// Utilidades
use Mail;
use Auth;
use App;
use View;
use DB;

class MailService
{

    public function activarCuenta($usuario, $password)
    {
        Mail::send('email.bienvenidaBusiness', ['usuario' => $usuario, 'password' => $password], function ($m) use ($usuario, $password) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, 'Transporter Business')->subject('Bienvenido a Transporter Business');
        });
    }

    public function datosRecogidaDomicilio($preferencia)
    {
        $usuario = Auth::guard('business')->user();

        Mail::send('email.notificacionRecogida', ['usuario' => $usuario, 'preferencia' => $preferencia], function ($m) use ($usuario, $preferencia) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to(env('MAIL_BUSINESS'), 'Transporter Business')->subject('Solicitud recogida periódica');
        });
    }

    public function envioPagado($envio)
    {
        if($envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }

        $ruta = route('tracking_index', ['localizador' => $envio->localizador]);

        Mail::send('email.envioPagadoBusiness', ['envio' => $envio, 'ruta' => $ruta], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($envio->destinatario->email, $envio->destinatario->nombre)->subject(\Lang::get('emails.envioPagado.asunto', ['nombre' => $envio->configuracionBusiness->nombre_comercial]));
        });
    }

    public function envioEnDestino($envio)
    {
        if($envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }



        Mail::send('email.destinoBusinessMR', ['envio' => $envio], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            if($envio->devolucionAsDevolucion) {
                $m->to($envio->configuracionBusiness->usuario->email, $envio->configuracionBusiness->usuario->configuracion->nombre)->subject(\Lang::get('emails.destinoMR.asunto', ['nombre' => $envio->configuracionBusiness->nombre_comercial]));
            } else {
                $m->to($envio->destinatario->email, $envio->destinatario->nombre)->subject(\Lang::get('emails.destinoMR.asunto', ['nombre' => $envio->configuracionBusiness->nombre_comercial]));
            }
        });
    }

    public function avisoDestinatario($envio)
    {
        if($envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }

        Mail::send('email.avisoDestinatarioBusinessMR', ['envio' => $envio], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($envio->destinatario->email, $envio->destinatario->nombre)->subject(\Lang::get('emails.avisoDestinatario.asunto'));
        });
    }

    public function cobroDevolucion($devolucion)
    {
        $devolucion = App\Models\DevolucionBusiness::find($devolucion->id);
        Mail::send('email.cobroDevolucionBusiness', ['devolucion' => $devolucion], function ($m) use ($devolucion) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to(env('MAIL_DEVOLUCIONES'), 'Transporter')->subject('Cobro devolución business');
        });
    }

    public function confirmarDevolucion($devolucion)
    {
        $devolucion = App\Models\DevolucionBusiness::find($devolucion->id);
        $destinatario = $devolucion->envio->destinatario;
        $ecommerce = $devolucion->envio->configuracionBusiness;

        $pdfFactura = null;
        $pdfEtiqueta = null;

        if($devolucion->envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }

        // Creamos factura
        if($devolucion->envio->coste_cliente_devolucion) {
            $pdfFactura = App::make('dompdf.wrapper');

            $peso = 0;
            foreach ($devolucion->motivosDevolucionProductos as $motivo) {
                $peso += $motivo->producto->peso;
            }
            $rango = App\Models\RangoBusiness::where([['min', '<', $peso], ['max', '>=', $peso]])->first();;
            $precio = $devolucion->envioDevolucion->precio;

            $view = View::make('business.files.facturaDevolucion', compact('devolucion', 'rango', 'precio'))->render();
            $pdfFactura->loadHTML($view);
        }

        // Creamos etiqueta
        if(!$devolucion->envio->etiqueta_preimpresa || ($devolucion->envio->devolucionesAsOriginal()->count() > 1 && !$devolucion->envioDevolucion->etiqueta_dual_carrier)) {
            if(($devolucion->envio->destino->codigoPostal->codigo_pais != 'DE' && $devolucion->envio->destino->codigoPostal->codigo_pais != 'AT') || $devolucion->envioDevolucion->etiqueta_dual_carrier) {
                $pdfEtiqueta = App::make('dompdf.wrapper');

                $envios = $devolucion->envioDevolucion()->get();

                $view = View::make('business.files.etiquetaDevolucion', compact('envios'))->render();
                $pdfEtiqueta->loadHTML($view);
            }
        }

        $ruta = route('tracking_index', ['localizador' => $devolucion->envioDevolucion->localizador]);

        Mail::send('email.devolucionBusinessConfirmada', ['devolucion' => $devolucion, 'ruta' => $ruta], function ($m) use ($ecommerce, $destinatario, $pdfFactura, $devolucion, $pdfEtiqueta) {
            if($devolucion->envio->coste_cliente_devolucion) {
                $m->attachData($pdfFactura->output(), \Lang::get('emails.devolucion.factura.nombre', ['nombre' => $ecommerce->nombre_comercial, 'fecha' => \Carbon::now()->toDateString()]));
            }
            if(!$devolucion->envio->etiqueta_preimpresa || $devolucion->envio->devolucionesAsOriginal()->count() > 1) {
                if(($devolucion->envio->destino->codigoPostal->codigo_pais != 'DE' && $devolucion->envio->destino->codigoPostal->codigo_pais != 'AT') || $devolucion->envioDevolucion->etiqueta_dual_carrier) {
                    $m->attachData($pdfEtiqueta->output(), \Lang::get('emails.devolucion.etiqueta.nombre', ['nombre' => $ecommerce->nombre_comercial, 'localizador' => $devolucion->envio->localizador]));
                } else {
                    $curlSession = curl_init();
                    curl_setopt($curlSession, CURLOPT_URL, $devolucion->envioDevolucion->etiqueta_dual_carrier);
                    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
                    $data = curl_exec($curlSession);
                    curl_close($curlSession);

                    $m->attachData($data, \Lang::get('emails.devolucion.etiqueta.nombre', ['nombre' => $ecommerce->nombre_comercial, 'localizador' => $devolucion->envio->localizador]));
                }
            }
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($destinatario->email, $destinatario->nombre)->subject(\Lang::get('emails.devolucion.asunto', ['nombre' => $ecommerce->nombre_comercial]));
        });
    }

    public function cancelacionEnvio($envio)
    {
        Mail::send('email.cancelacionEnvioBusiness', ['envio' => $envio], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to(env('MAIL_BUSINESS'), 'Transporter')->subject('Cancelación envío business');
        });
    }

    public function devolucionDisponible($envio)
    {
        $route = route('business_devolucion', ['pedido_id' => $envio->codigo]);

        if($envio->destino->codigoPostal->codigo_pais != 'ES') {
            \App::setLocale('en');
        } else {
            \App::setLocale('es');
        }

        Mail::send('email.devolucionDisponible', ['envio' => $envio, 'route' => $route], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($envio->destinatario->email, $envio->destinatario->nombre . ' ' . $envio->destinatario->apellidos)->subject(\Lang::get('emails.devolucionDisponible.asunto', ['nombre' => $envio->configuracionBusiness->nombre_comercial]));
        });
    }

}
