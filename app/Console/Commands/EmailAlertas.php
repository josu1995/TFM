<?php

namespace App\Console\Commands;

use App\Models\Alerta;
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Ruta;
use Illuminate\Console\Command;
use Mail;
use Log;

class EmailAlertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:alertas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envío de alertas a usuarios';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Recogemos alertas puntuales de mañana
        $datetime = new \DateTime('tomorrow');
        $tomorrow = date('Y-m-d',strtotime($datetime->format('Y-m-d H:i:s')));

        $alertasPuntuales = Alerta::where([['tipo_alertas_id', Alerta::PUNTUAL], ['fecha', $tomorrow], ['activo', '1']])->get();


        $dayOfWeek = date("w", strtotime($tomorrow));

        if ($dayOfWeek == 0) {
            $dayOfWeek = 7;
        }

        $alertasHabituales = Alerta::where([['tipo_alertas_id', Alerta::HABITUAL], ['dias', 'like', '%' . $dayOfWeek . '%'], ['activo', '1']])->get();


        foreach ($alertasPuntuales as $alerta) {
            $localidadOrigen = $alerta->origen_id;
            $localidadDestino = $alerta->destino_id;

            $envios = Envio::where('estado_id', Estado::ENTREGA)
                ->whereHas(
                    'puntoEntrega', function ($query) use ($localidadOrigen) {
                    $query->where('localidad_id', $localidadOrigen);
                })
                ->whereHas(
                    'puntoRecogida', function ($query) use ($localidadDestino) {
                    $query->where('localidad_id', $localidadDestino);
                })
                // Gestion de intermedios
                ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                    $query->where('estado_id', Estado::ENTREGA)
                        ->whereHas(
                            'puntoRecogida', function ($query) use ($localidadDestino, $localidadOrigen) {
                            $query->whereIn('localidad_id', function($query2) use ($localidadDestino, $localidadOrigen) {
                                $query2->select('localidad_fin_id')->from(with(new Ruta)->getTable())
                                    ->where([['localidad_inicio_id', $localidadOrigen], ['localidad_intermedia_id', $localidadDestino]]);
                            });
                        })
                        ->whereHas(
                            'puntoEntrega', function ($query) use ($localidadOrigen) {
                            $query->where('localidad_id', $localidadOrigen);
                        });
                })
                ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                    $query->where('estado_id', Estado::INTERMEDIO)
                        ->whereHas(
                            'puntoRecogida', function ($query) use ($localidadDestino) {
                            $query->where('localidad_id', $localidadDestino);
                        })
                        ->whereHas(
                            'posiciones', function ($query) use ($localidadDestino, $localidadOrigen) {
                            $query->whereHas(
                                'puntoDestino', function ($query) use ($localidadOrigen) {
                                $query->where('localidad_id', $localidadOrigen);
                            });
                        });
                })->get();

            if(count($envios) > 0) {
                $currentMail = $alerta->usuario->email;

                $subject = 'Envíos disponibles entre ' . $alerta->origen->nombre . ' y ' . $alerta->destino->nombre;

                Mail::send('email.alerta_viaje', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                    $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                    $m->to($currentMail)->subject($subject);
                });
            }
        }

        foreach ($alertasHabituales as $alerta) {
            $localidadOrigen = $alerta->origen_id;
            $localidadDestino = $alerta->destino_id;

            $envios = Envio::where('estado_id', Estado::ENTREGA)
                ->whereHas(
                    'puntoEntrega', function ($query) use ($localidadOrigen) {
                    $query->where('localidad_id', $localidadOrigen);
                })
                ->whereHas(
                    'puntoRecogida', function ($query) use ($localidadDestino) {
                    $query->where('localidad_id', $localidadDestino);
                })
                // Gestion de intermedios
                ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                    $query->where('estado_id', Estado::ENTREGA)
                        ->whereHas(
                            'puntoRecogida', function ($query) use ($localidadDestino, $localidadOrigen) {
                            $query->whereIn('localidad_id', function($query2) use ($localidadDestino, $localidadOrigen) {
                                $query2->select('localidad_fin_id')->from(with(new Ruta)->getTable())
                                    ->where([['localidad_inicio_id', $localidadOrigen], ['localidad_intermedia_id', $localidadDestino]]);
                            });
                        })
                        ->whereHas(
                            'puntoEntrega', function ($query) use ($localidadOrigen) {
                            $query->where('localidad_id', $localidadOrigen);
                        });
                })
                ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                    $query->where('estado_id', Estado::INTERMEDIO)
                        ->whereHas(
                            'puntoRecogida', function ($query) use ($localidadDestino) {
                            $query->where('localidad_id', $localidadDestino);
                        })
                        ->whereHas(
                            'posiciones', function ($query) use ($localidadDestino, $localidadOrigen) {
                            $query->whereHas(
                                'puntoDestino', function ($query) use ($localidadOrigen) {
                                $query->where('localidad_id', $localidadOrigen);
                            });
                        });
                })->get();

            if(count($envios) > 0) {
                $currentMail = $alerta->usuario->email;

                $subject = 'Envíos disponibles entre ' . $alerta->origen->nombre . ' y ' . $alerta->destino->nombre;

                Mail::send('email.alerta_viaje', ['email' => $currentMail, 'subject' => $subject, 'envios' => $envios, 'origen' => $alerta->origen, 'destino' => $alerta->destino], function ($m) use ($currentMail, $envios, $subject) {
                    $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                    $m->to($currentMail)->subject($subject);
                });
            }
        }

        Log::info('Daily schedule executed: Alertas');
    }
}
