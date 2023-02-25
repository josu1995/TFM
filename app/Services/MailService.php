<?php

namespace App\Services;

// Utilidades
use App\Models\Alerta;
use App\Models\Estado;
use Mail;
use Session;

// Modelos
use App\Models\Envio;
use App\Models\Punto;
use App\Models\Viaje;
use App\Models\Persona;

class MailService
{

    public function ruta(Envio $envio)
    {
        $usuario = $envio->usuario;

        Mail::send('email.ruta', ['usuario' => $usuario, 'envio' => $envio], function ($m) use ($usuario, $envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Tu envío ya está en camino');
        });
    }

    // Envío de mail a usuario al que se le ha asignado un punto
    public function punto(Punto $punto)
    {
        $usuario = $punto->usuario;

        Mail::send('email.punto', ['usuario' => $usuario, 'punto' => $punto], function ($m) use ($usuario, $punto) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Gestiona tu Transporter Store');
        });
    }

    // Envío de mail creación de viaje
    public function viaje(Viaje $viaje)
    {
        $usuario = $viaje->transportista;

        $origenes = array();
        $destinos = array();
        foreach (Session::get('envios_seleccionados') as $key => $envio) {
            if($envio['envio']->puntoEntrega->id != $envio['inicio']->id) {
                $origenes[$envio['inicio']->id] = $envio['inicio'];
                foreach($viaje->envios as $envioviaje) {
                    if($envioviaje->id == $envio['envio']->id) {
                        $envioviaje->puntoEntrega = $envio['inicio'];
                        break;
                    }
                }
            } else {
                $origenes[$envio['envio']->puntoEntrega->id] = $envio['envio']->puntoEntrega;
            }

            if($envio['envio']->puntoRecogida->id != $envio['fin']->id ) {
                $destinos[$envio['fin']->id] = $envio['fin'];
                foreach($viaje->envios as $envioviaje) {
                    if($envioviaje->id == $envio['envio']->id) {
                        $envioviaje->puntoRecogida = $envio['fin'];
                        break;
                    }
                }
            } else  {
                $destinos[$envio['envio']->puntoRecogida->id] = $envio['envio']->puntoRecogida;
            }
        }

        Mail::send('email.viaje', ['usuario' => $usuario, 'viaje' => $viaje, 'origenes' => $origenes, 'destinos' => $destinos], function ($m) use ($usuario, $viaje, $origenes, $destinos) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Detalles de tu viaje');
        });
    }

    public function viajeProfesional(Viaje $viaje, $origenes, $destinos)
    {
        $usuario = $viaje->transportista;

        $origenes = Punto::whereIn('id', $origenes)->get();
        $destinos = Punto::whereIn('id', $destinos)->get();

        Mail::send('email.viaje', ['usuario' => $usuario, 'viaje' => $viaje, 'origenes' => $origenes, 'destinos' => $destinos], function ($m) use ($usuario, $viaje, $origenes, $destinos) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Detalles de tu viaje');
        });
    }

    // Envío de mail a destinatario
    public function destinatario(Envio $envio)
    {
        $destinatario = $envio->destinatario;

        Mail::send('email.destinatario', ['destinatario' => $destinatario, 'envio' => $envio], function ($m) use ($destinatario, $envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($destinatario->email, $destinatario->nombre)->subject('Tu envío está listo para que puedas recogerlo');
        });
    }

    // Envío de mail a destinatario
    public function encuesta(Envio $envio)
    {
        $destinatario = $envio->destinatario;

        Mail::send('email.encuesta', ['destinatario' => $destinatario, 'envio' => $envio], function ($m) use ($destinatario, $envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($destinatario->email, $destinatario->nombre)->subject('Tu envío ha sido recogido.');
        });
    }

    // Envío de mail a emisor
    public function opinion(Envio $envio)
    {
        $emisor = $envio->usuario;

        Mail::send('email.encuesta', ['destinatario' => $emisor, 'envio' => $envio, 'opinion' => 1], function ($m) use ($emisor, $envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($emisor->email, $emisor->nombre)->subject('Tu envío ha sido recogido');
        });
    }

    // Envío de mail a transportista
    public function opinionTransportista(Viaje $viaje)
    {
        $transporter = $viaje->transportista;

        Mail::send('email.encuesta', ['destinatario' => $transporter, 'viaje' => $viaje, 'opinion' => 1], function ($m) use ($transporter, $viaje) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($transporter->email, $transporter->nombre)->subject('Tu viaje ha finalizado');
        });
    }

    // Mail para envio de alertas al entrar un paquete a un punto
    public function enviarAlertaEnOrigen(Envio $envio)
    {

        // Recogemos alertas puntuales de mañana
        $datetime = new \DateTime('tomorrow');
        $tomorrow = date('Y-m-d',strtotime($datetime->format('Y-m-d H:i:s')));

        $alertasPuntuales = Alerta::where([['tipo_alertas_id', Alerta::PUNTUAL], ['fecha', $tomorrow], ['origen_id', $envio->puntoEntrega->localidad->id], ['destino_id', $envio->puntoRecogida->localidad->id], ['activo', '1']])->get();


        $dayOfWeek = date("w", strtotime($tomorrow));

        if ($dayOfWeek == 0) {
            $dayOfWeek = 7;
        }

        $alertasHabituales = Alerta::where([['tipo_alertas_id', Alerta::HABITUAL], ['dias', 'like', '%' . $dayOfWeek . '%'], ['origen_id', $envio->puntoEntrega->localidad->id], ['destino_id', $envio->puntoRecogida->localidad->id], ['activo', '1']])->get();


        foreach ($alertasPuntuales as $alerta) {
            $localidadOrigen = $alerta->origen_id;
            $localidadDestino = $alerta->destino_id;

            $envios = $envio->pedido->envios()->where('estado_id', Estado::ENTREGA)
                ->whereHas(
                    'puntoEntrega', function ($query) use ($localidadOrigen) {
                    $query->where('localidad_id', $localidadOrigen);
                })
                ->whereHas(
                    'puntoRecogida', function ($query) use ($localidadDestino) {
                    $query->where('localidad_id', $localidadDestino);
                })->get();

            $currentMail = $alerta->usuario->email;

            $subject = 'Nuevos envíos disponibles entre '.$alerta->origen->nombre.' y '.$alerta->destino->nombre;

            Mail::send('email.alerta_nuevos_envios', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to($currentMail)->subject($subject);
            });
        }

        foreach ($alertasHabituales as $alerta) {
            $localidadOrigen = $alerta->origen_id;
            $localidadDestino = $alerta->destino_id;

            $envios = $envio->pedido->envios()->where('estado_id', Estado::ENTREGA)
                ->whereHas(
                    'puntoEntrega', function ($query) use ($localidadOrigen) {
                    $query->where('localidad_id', $localidadOrigen);
                })
                ->whereHas(
                    'puntoRecogida', function ($query) use ($localidadDestino) {
                    $query->where('localidad_id', $localidadDestino);
                })->get();

            $currentMail = $alerta->usuario->email;

            $subject = 'Nuevos envíos disponibles entre '.$alerta->origen->nombre.' y '.$alerta->destino->nombre;

            Mail::send('email.alerta_nuevos_envios', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to($currentMail)->subject($subject);
            });
        }
    }


    // Mail para envio de alertas al entrar un paquete a un punto
    public function enviarAlertaEnIntermedio(Envio $envio)
    {
        $posicion = $envio->posicionesSinCancelar()->whereNotNull('punto_destino_id')->first();
        if($posicion) {
            $origen = $posicion->puntoDestino->localidad->id;
            // Recogemos alertas puntuales de mañana
            $datetime = new \DateTime('tomorrow');
            $tomorrow = date('Y-m-d', strtotime($datetime->format('Y-m-d H:i:s')));

            $alertasPuntuales = Alerta::where([['tipo_alertas_id', Alerta::PUNTUAL], ['fecha', $tomorrow], ['origen_id', $origen], ['destino_id', $envio->puntoRecogida->localidad->id], ['activo', '1']])->get();


            $dayOfWeek = date("w", strtotime($tomorrow));

            if ($dayOfWeek == 0) {
                $dayOfWeek = 7;
            }

            $alertasHabituales = Alerta::where([['tipo_alertas_id', Alerta::HABITUAL], ['dias', 'like', '%' . $dayOfWeek . '%'], ['origen_id', $origen], ['destino_id', $envio->puntoRecogida->localidad->id], ['activo', '1']])->get();


            foreach ($alertasPuntuales as $alerta) {
                $localidadOrigen = $alerta->origen_id;
                $localidadDestino = $alerta->destino_id;

                $envios = $envio->viaje->envios()->where('estado_id', Estado::INTERMEDIO)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino) {
                        $query->where('localidad_id', $localidadDestino);
                    })->get();

                $currentMail = $alerta->usuario->email;

                $subject = 'Nuevos envíos disponibles entre ' . $alerta->origen->nombre . ' y ' . $alerta->destino->nombre;

                Mail::send('email.alerta_nuevos_envios', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                    $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                    $m->to($currentMail)->subject($subject);
                });
            }

            foreach ($alertasHabituales as $alerta) {
                $localidadOrigen = $alerta->origen_id;
                $localidadDestino = $alerta->destino_id;

                $envios = $envio->viajes->first()->envios()->where('estado_id', Estado::INTERMEDIO)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino) {
                        $query->where('localidad_id', $localidadDestino);
                    })->get();

                $currentMail = $alerta->usuario->email;

                $subject = 'Nuevos envíos disponibles entre ' . $alerta->origen->nombre . ' y ' . $alerta->destino->nombre;

                Mail::send('email.alerta_nuevos_envios', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                    $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                    $m->to($currentMail)->subject($subject);
                });
            }
        }
    }

    public function cancelarViaje($viaje) {
        setlocale(LC_TIME, 'es_ES.utf8');
        $origen = null;
        $destino = null;

        if(count($viaje->posiciones) == 0) {
            $origen = $viaje->envios[0]->puntoEntrega->localidad->nombre;
            $destino = $viaje->envios[0]->puntoRecogida->localidad->nombre;
        } else {
            foreach ($viaje->posiciones as $posicion) {
                if (!is_null($posicion->punto_origen_id)) {
                    $origen = $posicion->puntoOrigen->localidad->nombre;
                    $destino = $viaje->envios[0]->puntoRecogida->localidad->nombre;
                } else if (!is_null($posicion->punto_destino_id)) {
                    $origen = $viaje->envios[0]->puntoEntrega->localidad->nombre;
                    $destino = $posicion->puntoDestino->localidad->nombre;
                }
            }
        }

        Mail::send('email.cancelacionViaje', ['viaje' => $viaje, 'origen' => $origen, 'destino' => $destino], function ($m) use ($viaje) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($viaje->transportista->email, $viaje->transportista->configuracion->nombre)->subject('Tu viaje ha sido cancelado');
        });
    }

    public function avisoTransportista($viaje) {
        setlocale(LC_TIME, 'es_ES.utf8');
        $origen = null;
        $destino = null;

        if(count($viaje->posiciones) == 0) {
            $origen = $viaje->envios[0]->puntoEntrega->localidad->nombre;
            $destino = $viaje->envios[0]->puntoRecogida->localidad->nombre;
        } else {
            foreach ($viaje->posiciones as $posicion) {
                if (!is_null($posicion->punto_origen_id)) {
                    $origen = $posicion->puntoOrigen->localidad->nombre;
                    $destino = $viaje->envios[0]->puntoRecogida->localidad->nombre;
                } else if (!is_null($posicion->punto_destino_id)) {
                    $origen = $viaje->envios[0]->puntoEntrega->localidad->nombre;
                    $destino = $posicion->puntoDestino->localidad->nombre;
                }
            }
        }

        Mail::send('email.avisoViaje', ['viaje' => $viaje, 'origen' => $origen, 'destino' => $destino], function ($m) use ($viaje) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($viaje->transportista->email, $viaje->transportista->configuracion->nombre)->subject('Tu viaje se está demorando');
        });
    }

    public function avisoDestinatario($envio) {

        Mail::send('email.avisoDestinatario', ['envio' => $envio], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($envio->destinatario->email, $envio->destinatario->nombre)->subject('Tu envío lleva una semana en destino');
        });
    }

    public function cobroDevolucion($envio) {

        Mail::send('email.cobroDevolucion', ['envio' => $envio], function ($m) use ($envio) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to(env('MAIL_DEVOLUCIONES'), 'Devoluciones')->subject('Cobro por devolución');
        });
    }

}
