<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Jobs\EtiquetaDualCarrier;
use App\Models\CodigoPostal;
use App\Models\EnvioMondialRelay;
use App\Models\ErrorMondialRelay;
use App\Models\Horario;
use App\Models\HorarioMondialRelay;
use App\Models\Imagen;
use App\Models\MondialRelayStore;
use App\Models\Punto;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use Carbon\Carbon;
use Config;
use Mail;
use Session;


class MondialRelayService
{

    private $client;
    private $clientDualCarrier;

    public function __construct()
    {
        $this->client = new \nusoap_client(env('MONDIAL_RELAY_WSDL'), true);
        $this->clientDualCarrier = new \nusoap_client(env('MONDIAL_RELAY_DUAL_CARRIER'));
    }

    public function call($params, $endpoint)
    {

        $MR_WebSiteKey = env('MONDIAL_RELAY_PK');
        $this->client->soap_defencoding = 'utf-8';

        $code = implode("", $params);
        $code .= $MR_WebSiteKey;
        $params["Security"] = strtoupper(md5($code));

        return $this->client->call(
            $endpoint,
            $params,
            'http://api.mondialrelay.com/',
            'http://api.mondialrelay.com/' . $endpoint
        );
    }

    public function callDualCarrier($params)
    {

        return $this->clientDualCarrier->call(
            'ShipmentCreationRequest',
            $params,
            'http://connect.api.sandbox.mondialrelay.com',
            env('MONDIAL_RELAY_DUAL_CARRIER')
        );
    }

    public function getPuntos($params)
    {
      
        $endpoint = Config::get('enums.mondialRelayEndpoints.getPuntos4');
        $response = $this->call($params, $endpoint);
       

        if ($response['WSI4_PointRelais_RechercheResult']['STAT'] == 0) {
            if ($response['WSI4_PointRelais_RechercheResult']['PointsRelais'] != '') {
                $points = $response['WSI4_PointRelais_RechercheResult']['PointsRelais']['PointRelais_Details'];
                if (array_key_exists('STAT', $points)) {
                    $points = [$points];
                }
                $puntos = [];
                foreach ($points as $point) {
                    $punto = new Punto();
                    $punto->id = Config::get('enums.tiposStores.puntoPack') . trim($point['Num']);
                    $punto->tipo = Config::get('enums.tiposStores.puntoPack');
                    $punto->metodoEnvio = 'InPost';
                    $punto->nombre = ucfirst(strtolower(trim($point['LgAdr1'])));
                    $punto->latitud = strval(
                        floatval(
                            number_format(floatval(str_replace(',', '.', trim($point['Latitude']))), 6)
                        )
                    );
                    $punto->longitud = strval(
                        floatval(number_format(floatval(str_replace(',', '.', trim($point['Longitude']))), 6))
                    );
                    $punto->direccion = ucfirst(trim($point['LgAdr3']));
                    $punto->codigo_postal = trim($point['CP']);
                    $punto->codigo_postal = $punto->codigoPostal()->where('codigo_pais', trim($point['Pays']))->first();
                    
                    $imagen = new Imagen();
                    $imagen->path = trim($point['URL_Photo'] ?? '');
                    $punto->imagen = $imagen;

                    $horarios = [];

                    $dayOfWeek = now()->createFromFormat('Y-m-d', Session::get('fecha'))->dayOfWeek;

                    $lunesManana = new Horario();
                    $lunesManana->dia = 1;
                    if ($point['Horaires_Lundi']['string'][0] === '0000') {
                        $lunesManana->cerrado = 1;
                    } else {
                        $lunesManana->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][0]);
                        $lunesManana->fin = $this->formatHorario($point['Horaires_Lundi']['string'][1]);
                    }
                    array_push($horarios, $lunesManana);
                    $lunesTarde = new Horario();
                    $lunesTarde->dia = 1;
                    if ($point['Horaires_Lundi']['string'][2] === '0000') {
                        $lunesTarde->cerrado = 1;
                    } else {
                        $lunesTarde->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][2]);
                        $lunesTarde->fin = $this->formatHorario($point['Horaires_Lundi']['string'][3]);
                    }
                    array_push($horarios, $lunesTarde);
                    if ($lunesManana->dia == $dayOfWeek) {
                        $punto->hoy = [$lunesManana, $lunesTarde];
                    }

                    $martesManana = new Horario();
                    $martesManana->dia = 2;
                    if ($point['Horaires_Mardi']['string'][0] === '0000') {
                        $martesManana->cerrado = 1;
                    } else {
                        $martesManana->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][0]);
                        $martesManana->fin = $this->formatHorario($point['Horaires_Mardi']['string'][1]);
                    }
                    array_push($horarios, $martesManana);
                    $martesTarde = new Horario();
                    $martesTarde->dia = 2;
                    if ($point['Horaires_Mardi']['string'][2] === '0000') {
                        $martesTarde->cerrado = 1;
                    } else {
                        $martesTarde->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][2]);
                        $martesTarde->fin = $this->formatHorario($point['Horaires_Mardi']['string'][3]);
                    }
                    array_push($horarios, $martesTarde);
                    if ($martesManana->dia == $dayOfWeek) {
                        $punto->hoy = [$martesManana, $martesTarde];
                    }

                    $miercolesManana = new Horario();
                    $miercolesManana->dia = 3;
                    if ($point['Horaires_Mercredi']['string'][0] === '0000') {
                        $miercolesManana->cerrado = 1;
                    } else {
                        $miercolesManana->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][0]);
                        $miercolesManana->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][1]);
                    }
                    array_push($horarios, $miercolesManana);
                    $miercolesTarde = new Horario();
                    $miercolesTarde->dia = 3;
                    if ($point['Horaires_Mercredi']['string'][2] === '0000') {
                        $miercolesTarde->cerrado = 1;
                    } else {
                        $miercolesTarde->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][2]);
                        $miercolesTarde->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][3]);
                    }
                    array_push($horarios, $miercolesTarde);
                    if ($miercolesManana->dia == $dayOfWeek) {
                        $punto->hoy = [$miercolesManana, $miercolesTarde];
                    }

                    $juevesManana = new Horario();
                    $juevesManana->dia = 4;
                    if ($point['Horaires_Jeudi']['string'][0] === '0000') {
                        $juevesManana->cerrado = 1;
                    } else {
                        $juevesManana->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][0]);
                        $juevesManana->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][1]);
                    }
                    array_push($horarios, $juevesManana);
                    $juevesTarde = new Horario();
                    $juevesTarde->dia = 4;
                    if ($point['Horaires_Jeudi']['string'][2] === '0000') {
                        $juevesTarde->cerrado = 1;
                    } else {
                        $juevesTarde->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][2]);
                        $juevesTarde->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][3]);
                    }
                    array_push($horarios, $juevesTarde);
                    if ($juevesManana->dia == $dayOfWeek) {
                        $punto->hoy = [$juevesManana, $juevesTarde];
                    }

                    $viernesManana = new Horario();
                    $viernesManana->dia = 5;
                    if ($point['Horaires_Vendredi']['string'][0] === '0000') {
                        $viernesManana->cerrado = 1;
                    } else {
                        $viernesManana->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][0]);
                        $viernesManana->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][1]);
                    }
                    array_push($horarios, $viernesManana);
                    $viernesTarde = new Horario();
                    $viernesTarde->dia = 5;
                    if ($point['Horaires_Vendredi']['string'][2] === '0000') {
                        $viernesTarde->cerrado = 1;
                    } else {
                        $viernesTarde->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][2]);
                        $viernesTarde->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][3]);
                    }
                    array_push($horarios, $viernesTarde);
                    if ($viernesManana->dia == $dayOfWeek) {
                        $punto->hoy = [$viernesManana, $viernesTarde];
                    }

                    $sabadoManana = new Horario();
                    $sabadoManana->dia = 6;
                    if ($point['Horaires_Samedi']['string'][0] === '0000') {
                        $sabadoManana->cerrado = 1;
                    } else {
                        $sabadoManana->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][0]);
                        $sabadoManana->fin = $this->formatHorario($point['Horaires_Samedi']['string'][1]);
                    }
                    array_push($horarios, $sabadoManana);
                    $sabadoTarde = new Horario();
                    $sabadoTarde->dia = 6;
                    if ($point['Horaires_Samedi']['string'][2] === '0000') {
                        $sabadoTarde->cerrado = 1;
                    } else {
                        $sabadoTarde->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][2]);
                        $sabadoTarde->fin = $this->formatHorario($point['Horaires_Samedi']['string'][3]);
                    }
                    array_push($horarios, $sabadoTarde);
                    if ($sabadoManana->dia == $dayOfWeek) {
                        $punto->hoy = [$sabadoManana, $sabadoTarde];
                    }

                    $domingoManana = new Horario();
                    $domingoManana->dia = 7;
                    if ($point['Horaires_Dimanche']['string'][0] === '0000') {
                        $domingoManana->cerrado = 1;
                    } else {
                        $domingoManana->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][0]);
                        $domingoManana->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][1]);
                    }
                    array_push($horarios, $domingoManana);
                    $domingoTarde = new Horario();
                    $domingoTarde->dia = 7;
                    if ($point['Horaires_Dimanche']['string'][2] === '0000') {
                        $domingoTarde->cerrado = 1;
                    } else {
                        $domingoTarde->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][2]);
                        $domingoTarde->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][3]);
                    }
                    array_push($horarios, $domingoTarde);
                    if (0 == $dayOfWeek) {
                        $punto->hoy = [$domingoManana, $domingoTarde];
                    }

                    $punto->horarios = $horarios;

                    if ($point['Informations_Dispo'] != '') {
                        $inicioVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Debut']);
                        $finVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Fin']);
                        $now = Carbon::now();
                        if ($now->gte($inicioVacaciones) && $now->lte($finVacaciones)) {
                            continue;
                        }
                    }
                    array_push($puntos, $punto);
                }
            } else {
                $puntos = array();
            }

            return $puntos;
        }

        return null;
    }

    public function getPunto($params)
    {

        $endpoint = Config::get('enums.mondialRelayEndpoints.getPuntos4');

        $response = $this->call($params, $endpoint);

        if ($response['WSI3_PointRelais_RechercheResult']['STAT'] == 0) {
            $point = $response['WSI3_PointRelais_RechercheResult']['PointsRelais']['PointRelais_Details'];
            $punto = new Punto();
            $punto->id = Config::get('enums.tiposStores.puntoPack') . trim($point['Num']);
            $punto->tipo = Config::get('enums.tiposStores.puntoPack');
            $punto->nombre = ucfirst(strtolower(trim($point['LgAdr1'])));
            $punto->latitud = strval(
                floatval(number_format(floatval(str_replace(',', '.', trim($point['Latitude']))), 6))
            );
            $punto->longitud = strval(
                floatval(number_format(floatval(str_replace(',', '.', trim($point['Longitude']))), 6))
            );
            $punto->direccion = ucfirst(trim($point['LgAdr3']));
            $punto->codigo_postal = trim($point['CP']);
            $imagen = new Imagen();
            $imagen->path = trim($point['URL_Photo']);
            $punto->imagen = $imagen;

            $horarios = [];

            $dayOfWeek = Carbon::now()->dayOfWeek;

            $lunesManana = new Horario();
            $lunesManana->dia = 1;
            if ($point['Horaires_Lundi']['string'][0] === '0000') {
                $lunesManana->cerrado = 1;
            } else {
                $lunesManana->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][0]);
                $lunesManana->fin = $this->formatHorario($point['Horaires_Lundi']['string'][1]);
            }
            array_push($horarios, $lunesManana);
            $lunesTarde = new Horario();
            $lunesTarde->dia = 1;
            if ($point['Horaires_Lundi']['string'][2] === '0000') {
                $lunesTarde->cerrado = 1;
            } else {
                $lunesTarde->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][2]);
                $lunesTarde->fin = $this->formatHorario($point['Horaires_Lundi']['string'][3]);
            }
            array_push($horarios, $lunesTarde);
            if ($lunesManana->dia == $dayOfWeek) {
                $punto->hoy = [$lunesManana, $lunesTarde];
            }

            $martesManana = new Horario();
            $martesManana->dia = 2;
            if ($point['Horaires_Mardi']['string'][0] === '0000') {
                $martesManana->cerrado = 1;
            } else {
                $martesManana->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][0]);
                $martesManana->fin = $this->formatHorario($point['Horaires_Mardi']['string'][1]);
            }
            array_push($horarios, $martesManana);
            $martesTarde = new Horario();
            $martesTarde->dia = 2;
            if ($point['Horaires_Mardi']['string'][2] === '0000') {
                $martesTarde->cerrado = 1;
            } else {
                $martesTarde->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][2]);
                $martesTarde->fin = $this->formatHorario($point['Horaires_Mardi']['string'][3]);
            }
            array_push($horarios, $martesTarde);
            if ($martesManana->dia == $dayOfWeek) {
                $punto->hoy = [$martesManana, $martesTarde];
            }

            $miercolesManana = new Horario();
            $miercolesManana->dia = 3;
            if ($point['Horaires_Mercredi']['string'][0] === '0000') {
                $miercolesManana->cerrado = 1;
            } else {
                $miercolesManana->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][0]);
                $miercolesManana->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][1]);
            }
            array_push($horarios, $miercolesManana);
            $miercolesTarde = new Horario();
            $miercolesTarde->dia = 3;
            if ($point['Horaires_Mercredi']['string'][2] === '0000') {
                $miercolesTarde->cerrado = 1;
            } else {
                $miercolesTarde->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][2]);
                $miercolesTarde->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][3]);
            }
            array_push($horarios, $miercolesTarde);
            if ($miercolesManana->dia == $dayOfWeek) {
                $punto->hoy = [$miercolesManana, $miercolesTarde];
            }

            $juevesManana = new Horario();
            $juevesManana->dia = 4;
            if ($point['Horaires_Jeudi']['string'][0] === '0000') {
                $juevesManana->cerrado = 1;
            } else {
                $juevesManana->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][0]);
                $juevesManana->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][1]);
            }
            array_push($horarios, $juevesManana);
            $juevesTarde = new Horario();
            $juevesTarde->dia = 4;
            if ($point['Horaires_Jeudi']['string'][2] === '0000') {
                $juevesTarde->cerrado = 1;
            } else {
                $juevesTarde->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][2]);
                $juevesTarde->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][3]);
            }
            array_push($horarios, $juevesTarde);
            if ($juevesManana->dia == $dayOfWeek) {
                $punto->hoy = [$juevesManana, $juevesTarde];
            }

            $viernesManana = new Horario();
            $viernesManana->dia = 5;
            if ($point['Horaires_Vendredi']['string'][0] === '0000') {
                $viernesManana->cerrado = 1;
            } else {
                $viernesManana->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][0]);
                $viernesManana->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][1]);
            }
            array_push($horarios, $viernesManana);
            $viernesTarde = new Horario();
            $viernesTarde->dia = 5;
            if ($point['Horaires_Vendredi']['string'][2] === '0000') {
                $viernesTarde->cerrado = 1;
            } else {
                $viernesTarde->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][2]);
                $viernesTarde->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][3]);
            }
            array_push($horarios, $viernesTarde);
            if ($viernesManana->dia == $dayOfWeek) {
                $punto->hoy = [$viernesManana, $viernesTarde];
            }

            $sabadoManana = new Horario();
            $sabadoManana->dia = 6;
            if ($point['Horaires_Samedi']['string'][0] === '0000') {
                $sabadoManana->cerrado = 1;
            } else {
                $sabadoManana->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][0]);
                $sabadoManana->fin = $this->formatHorario($point['Horaires_Samedi']['string'][1]);
            }
            array_push($horarios, $sabadoManana);
            $sabadoTarde = new Horario();
            $sabadoTarde->dia = 6;
            if ($point['Horaires_Samedi']['string'][2] === '0000') {
                $sabadoTarde->cerrado = 1;
            } else {
                $sabadoTarde->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][2]);
                $sabadoTarde->fin = $this->formatHorario($point['Horaires_Samedi']['string'][3]);
            }
            array_push($horarios, $sabadoTarde);
            if ($sabadoManana->dia == $dayOfWeek) {
                $punto->hoy = [$sabadoManana, $sabadoTarde];
            }

            $domingoManana = new Horario();
            $domingoManana->dia = 7;
            if ($point['Horaires_Dimanche']['string'][0] === '0000') {
                $domingoManana->cerrado = 1;
            } else {
                $domingoManana->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][0]);
                $domingoManana->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][1]);
            }
            array_push($horarios, $domingoManana);
            $domingoTarde = new Horario();
            $domingoTarde->dia = 7;
            if ($point['Horaires_Dimanche']['string'][2] === '0000') {
                $domingoTarde->cerrado = 1;
            } else {
                $domingoTarde->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][2]);
                $domingoTarde->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][3]);
            }
            array_push($horarios, $domingoTarde);
            if (0 == $dayOfWeek) {
                $punto->hoy = [$domingoManana, $domingoTarde];
            }

            $punto->horarios = $horarios;

            if ($point['Informations_Dispo'] != '') {
                $inicioVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Debut']);
                $finVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Fin']);
                $now = Carbon::now();
                if ($now->gte($inicioVacaciones) && $now->lte($finVacaciones)) {
                    //TODO: Cerrado
                }
            }

            // Guardamos en stores de Mondial Relay
            $store = MondialRelayStore::find(trim($point['Num']));
            if (!$store) {
                $store = new MondialRelayStore();
            } else {
                HorarioMondialRelay::where('store_id', $store->id)->delete();
            }
            $store->id = trim($point['Num']);
            $store->nombre = $punto->nombre;
            $store->direccion = $punto->direccion;
            $store->cp_id = CodigoPostal::where([
                ['codigo_postal', $punto->codigo_postal],
                ['codigo_pais', $point['Pays']]
            ])->whereHas('pais', function ($query) {
                $query->whereNotNull('zona_id');
            })->first()->id;
            $store->latitud = $punto->latitud;
            $store->longitud = $punto->longitud;
            $store->imagen = trim($point['URL_Photo']);
            $store->save();

            foreach ($horarios as $horario) {
                $newHorario = new HorarioMondialRelay();
                $newHorario->dia = $horario->dia;
                $newHorario->inicio = $horario->inicio;
                $newHorario->fin = $horario->fin;
                $newHorario->cerrado = $horario->cerrado ? 1 : 0;
                $newHorario->store_id = trim($point['Num']);
                $newHorario->save();
            }

            return $punto;
        }

        return null;
    }

    public function getPuntoCercano($params)
    {

        try {
            $endpoint = Config::get('enums.mondialRelayEndpoints.getPuntos4');

            $response = $this->call($params, $endpoint);

            if ($response['WSI4_PointRelais_RechercheResult']['STAT'] == 0 &&
                $response['WSI4_PointRelais_RechercheResult']['PointsRelais'] !== '') {
                $point = $response['WSI4_PointRelais_RechercheResult']['PointsRelais']['PointRelais_Details'];
                $punto = new Punto();
                $punto->id = Config::get('enums.tiposStores.puntoPack') . trim($point['Num']);
                $punto->tipo = Config::get('enums.tiposStores.puntoPack');
                $punto->nombre = ucfirst(strtolower(trim($point['LgAdr1'])));
                $punto->latitud = strval(
                    floatval(number_format(floatval(str_replace(',', '.', trim($point['Latitude']))), 6))
                );
                $punto->longitud = strval(
                    floatval(number_format(floatval(str_replace(',', '.', trim($point['Longitude']))), 6))
                );
                $punto->direccion = ucfirst(trim($point['LgAdr3']));
                $punto->codigo_postal = trim($point['CP']);
                $imagen = new Imagen();
                $imagen->path = trim($point['URL_Photo']);
                $punto->imagen = $imagen;

                $horarios = [];

                $dayOfWeek = Carbon::now()->dayOfWeek;

                $lunesManana = new Horario();
                $lunesManana->dia = 1;
                if ($point['Horaires_Lundi']['string'][0] === '0000') {
                    $lunesManana->cerrado = 1;
                } else {
                    $lunesManana->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][0]);
                    $lunesManana->fin = $this->formatHorario($point['Horaires_Lundi']['string'][1]);
                }
                array_push($horarios, $lunesManana);
                $lunesTarde = new Horario();
                $lunesTarde->dia = 1;
                if ($point['Horaires_Lundi']['string'][2] === '0000') {
                    $lunesTarde->cerrado = 1;
                } else {
                    $lunesTarde->inicio = $this->formatHorario($point['Horaires_Lundi']['string'][2]);
                    $lunesTarde->fin = $this->formatHorario($point['Horaires_Lundi']['string'][3]);
                }
                array_push($horarios, $lunesTarde);
                if ($lunesManana->dia == $dayOfWeek) {
                    $punto->hoy = [$lunesManana, $lunesTarde];
                }

                $martesManana = new Horario();
                $martesManana->dia = 2;
                if ($point['Horaires_Mardi']['string'][0] === '0000') {
                    $martesManana->cerrado = 1;
                } else {
                    $martesManana->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][0]);
                    $martesManana->fin = $this->formatHorario($point['Horaires_Mardi']['string'][1]);
                }
                array_push($horarios, $martesManana);
                $martesTarde = new Horario();
                $martesTarde->dia = 2;
                if ($point['Horaires_Mardi']['string'][2] === '0000') {
                    $martesTarde->cerrado = 1;
                } else {
                    $martesTarde->inicio = $this->formatHorario($point['Horaires_Mardi']['string'][2]);
                    $martesTarde->fin = $this->formatHorario($point['Horaires_Mardi']['string'][3]);
                }
                array_push($horarios, $martesTarde);
                if ($martesManana->dia == $dayOfWeek) {
                    $punto->hoy = [$martesManana, $martesTarde];
                }

                $miercolesManana = new Horario();
                $miercolesManana->dia = 3;
                if ($point['Horaires_Mercredi']['string'][0] === '0000') {
                    $miercolesManana->cerrado = 1;
                } else {
                    $miercolesManana->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][0]);
                    $miercolesManana->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][1]);
                }
                array_push($horarios, $miercolesManana);
                $miercolesTarde = new Horario();
                $miercolesTarde->dia = 3;
                if ($point['Horaires_Mercredi']['string'][2] === '0000') {
                    $miercolesTarde->cerrado = 1;
                } else {
                    $miercolesTarde->inicio = $this->formatHorario($point['Horaires_Mercredi']['string'][2]);
                    $miercolesTarde->fin = $this->formatHorario($point['Horaires_Mercredi']['string'][3]);
                }
                array_push($horarios, $miercolesTarde);
                if ($miercolesManana->dia == $dayOfWeek) {
                    $punto->hoy = [$miercolesManana, $miercolesTarde];
                }

                $juevesManana = new Horario();
                $juevesManana->dia = 4;
                if ($point['Horaires_Jeudi']['string'][0] === '0000') {
                    $juevesManana->cerrado = 1;
                } else {
                    $juevesManana->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][0]);
                    $juevesManana->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][1]);
                }
                array_push($horarios, $juevesManana);
                $juevesTarde = new Horario();
                $juevesTarde->dia = 4;
                if ($point['Horaires_Jeudi']['string'][2] === '0000') {
                    $juevesTarde->cerrado = 1;
                } else {
                    $juevesTarde->inicio = $this->formatHorario($point['Horaires_Jeudi']['string'][2]);
                    $juevesTarde->fin = $this->formatHorario($point['Horaires_Jeudi']['string'][3]);
                }
                array_push($horarios, $juevesTarde);
                if ($juevesManana->dia == $dayOfWeek) {
                    $punto->hoy = [$juevesManana, $juevesTarde];
                }

                $viernesManana = new Horario();
                $viernesManana->dia = 5;
                if ($point['Horaires_Vendredi']['string'][0] === '0000') {
                    $viernesManana->cerrado = 1;
                } else {
                    $viernesManana->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][0]);
                    $viernesManana->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][1]);
                }
                array_push($horarios, $viernesManana);
                $viernesTarde = new Horario();
                $viernesTarde->dia = 5;
                if ($point['Horaires_Vendredi']['string'][2] === '0000') {
                    $viernesTarde->cerrado = 1;
                } else {
                    $viernesTarde->inicio = $this->formatHorario($point['Horaires_Vendredi']['string'][2]);
                    $viernesTarde->fin = $this->formatHorario($point['Horaires_Vendredi']['string'][3]);
                }
                array_push($horarios, $viernesTarde);
                if ($viernesManana->dia == $dayOfWeek) {
                    $punto->hoy = [$viernesManana, $viernesTarde];
                }

                $sabadoManana = new Horario();
                $sabadoManana->dia = 6;
                if ($point['Horaires_Samedi']['string'][0] === '0000') {
                    $sabadoManana->cerrado = 1;
                } else {
                    $sabadoManana->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][0]);
                    $sabadoManana->fin = $this->formatHorario($point['Horaires_Samedi']['string'][1]);
                }
                array_push($horarios, $sabadoManana);
                $sabadoTarde = new Horario();
                $sabadoTarde->dia = 6;
                if ($point['Horaires_Samedi']['string'][2] === '0000') {
                    $sabadoTarde->cerrado = 1;
                } else {
                    $sabadoTarde->inicio = $this->formatHorario($point['Horaires_Samedi']['string'][2]);
                    $sabadoTarde->fin = $this->formatHorario($point['Horaires_Samedi']['string'][3]);
                }
                array_push($horarios, $sabadoTarde);
                if ($sabadoManana->dia == $dayOfWeek) {
                    $punto->hoy = [$sabadoManana, $sabadoTarde];
                }

                $domingoManana = new Horario();
                $domingoManana->dia = 7;
                if ($point['Horaires_Dimanche']['string'][0] === '0000') {
                    $domingoManana->cerrado = 1;
                } else {
                    $domingoManana->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][0]);
                    $domingoManana->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][1]);
                }
                array_push($horarios, $domingoManana);
                $domingoTarde = new Horario();
                $domingoTarde->dia = 7;
                if ($point['Horaires_Dimanche']['string'][2] === '0000') {
                    $domingoTarde->cerrado = 1;
                } else {
                    $domingoTarde->inicio = $this->formatHorario($point['Horaires_Dimanche']['string'][2]);
                    $domingoTarde->fin = $this->formatHorario($point['Horaires_Dimanche']['string'][3]);
                }
                array_push($horarios, $domingoTarde);
                if (0 == $dayOfWeek) {
                    $punto->hoy = [$domingoManana, $domingoTarde];
                }

                $punto->horarios = $horarios;

                if ($point['Informations_Dispo'] != '') {
                    $inicioVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Debut']);
                    $finVacaciones = Carbon::parse($point['Informations_Dispo']['Periode']['Fin']);
                    $now = Carbon::now();
                    if ($now->gte($inicioVacaciones) && $now->lte($finVacaciones)) {
                        //TODO: Cerrado
                    }
                }

                // Guardamos en stores de Mondial Relay
                $store = MondialRelayStore::find(trim($point['Num']));
                if (!$store) {
                    $store = new MondialRelayStore();
                } else {
                    HorarioMondialRelay::where('store_id', $store->id)->delete();
                }
                $store->id = trim($point['Num']);
                $store->nombre = $punto->nombre;
                $store->direccion = $punto->direccion;
                $store->cp_id = CodigoPostal::where([
                    ['codigo_postal', $punto->codigo_postal],
                    ['codigo_pais', $point['Pays']]
                ])->whereHas('pais', function ($query) {
                    $query->whereNotNull('zona_id');
                })->first()->id;
                $store->latitud = $punto->latitud;
                $store->longitud = $punto->longitud;
                $store->imagen = trim($point['URL_Photo']);
                $store->save();

                foreach ($horarios as $horario) {
                    $newHorario = new HorarioMondialRelay();
                    $newHorario->dia = $horario->dia;
                    $newHorario->inicio = $horario->inicio;
                    $newHorario->fin = $horario->fin;
                    $newHorario->cerrado = $horario->cerrado ? 1 : 0;
                    $newHorario->store_id = trim($point['Num']);
                    $newHorario->save();
                }

                return $punto;
            }

        } catch (\Exception $e) {
            \Log::error('Excepcion en getPuntoCercano. Params: ' . print_r($params, true));
            \Log::error($e->getMessage());
        }

        return null;
    }

    public function getTracking($params)
    {
        $endpoint = Config::get('enums.mondialRelayEndpoints.getTracking');

        $response = $this->call($params, $endpoint);

        if (!$this->client->getError()) {
            if ($response['WSI2_TracingColisDetailleResult']['STAT'] == 80 ||
                $response['WSI2_TracingColisDetailleResult']['STAT'] == 81 ||
                $response['WSI2_TracingColisDetailleResult']['STAT'] == 82) {
                return $response['WSI2_TracingColisDetailleResult']['Tracing']['ret_WSI2_sub_TracingColisDetaille'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private function formatHorario($hora)
    {
        return substr($hora, 0, 2) . ':' . substr($hora, 2, 2);
    }

    public function crearEnvio($envio)
    {

        $colMode = '';
        $delivMode = '';
        $direccionOrigen = '';
        $ciudadOrigen = '';
        $cpOrigen = '';
        $paisOrigen = '';
        $idStoreMROrigen = null;
        $idStoreMRDestino = null;
        $paisDestino = '';
        if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
            $preferencia = $envio->preferenciaRecogida;
            $ciudadOrigen = $preferencia->codigoPostal->ciudad;
            $cpOrigen = $preferencia->codigoPostal->codigo_postal;
            $paisOrigen = $preferencia->codigoPostal->codigo_pais;
            if ($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $colMode = 'CCC';
                $direccionOrigen = $preferencia->direccion;
            } elseif ($preferencia->tipo_recogida_id == TiposRecogidaBusiness::STORE && $preferencia->store) {
                $colMode = 'CCC';
                $direccionOrigen = $preferencia->store->direccion;
            } else {
                $colMode = 'REL';
                $direccionOrigen = $preferencia->mondialRelayStore->direccion;
                $idStoreMROrigen = $preferencia->store_id;
            }
        } else {
            $ciudadOrigen = $envio->origen->codigoPostal->ciudad;
            if (strlen($ciudadOrigen) > 26) {
                $ciudadOrigen = substr($ciudadOrigen, 0, 26);
            }
            $cpOrigen = $envio->origen->codigoPostal->codigo_postal;
            $paisOrigen = $envio->origen->codigoPostal->codigo_pais;
            if ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $colMode = 'CCC';
                $direccionOrigen = $envio->origen->direccion;
            } elseif ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE && $envio->origen->store) {
                $colMode = 'CCC';
                $direccionOrigen = $envio->origen->store->direccion;
            } else {
                $colMode = 'REL';
                $direccionOrigen = $envio->origen->mondialRelayStore ?
                    $envio->origen->mondialRelayStore->direccion :
                    $envio->origen->store->direccion;
                $idStoreMROrigen = $envio->origen->store_id;
            }
        }

        $paisDestino = $envio->destino->codigoPostal->codigo_pais;
        if ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            if ($envio->destino->codigoPostal->codigo_pais == 'FR') {
                $delivMode = 'HOC';
            } else {
                $delivMode = 'HOM';
            }
            $direccionDestino = $envio->destino->direccion;
        } elseif ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE && $envio->destino->store) {
            if ($envio->destino->codigoPostal->codigo_pais == 'FR') {
                $delivMode = 'HOC';
            } else {
                $delivMode = 'HOM';
            }
            $direccionDestino = $envio->destino->store->direccion;
        } else {
            $delivMode = '24R';
            $direccionDestino = $envio->destino->mondialRelayStore->direccion;
            $idStoreMRDestino = $envio->destino->store_id;
        }

        $ciudadDestino = $envio->destino->codigoPostal->ciudad;
        if (strlen($ciudadDestino) > 26) {
            $ciudadDestino = substr($ciudadDestino, 0, 26);
        }

        $peso = 0;
        foreach ($envio->productos as $producto) {
            $peso += $producto->peso * $producto->pivot->cantidad;
        }

        if ($peso < 0.15) {
            $peso = 0.15;
        }

        $direccionOrigen2 = '';
        if (strlen($this->eliminarTildes($direccionOrigen)) > 32) {
            $split = explode(' ', $this->eliminarTildes($direccionOrigen));
            $direccionOrigen = '';
            $full = false;
            foreach ($split as $word) {
                if (strlen($direccionOrigen . ' ' . $word) > 32 && !$full) {
                    $full = true;
                    $direccionOrigen2 .= ' ' . $word;
                } elseif (!$full) {
                    $direccionOrigen .= ' ' . $word;
                } else {
                    $direccionOrigen2 .= ' ' . $word;
                }
            }
            if (strlen($direccionOrigen2) > 32) {
                $direccionOrigen2 = substr($direccionOrigen2, 0, 32);
            }
        }

        $direccionDestino2 = '';
        if (strlen($this->eliminarTildes($direccionDestino)) > 32) {
            $split = explode(' ', $this->eliminarTildes($direccionDestino));
            $direccionDestino = '';
            $full = false;
            foreach ($split as $word) {
                if (strlen($direccionDestino . ' ' . $word) > 32 && !$full) {
                    $full = true;
                    $direccionDestino2 .= ' ' . $word;
                } elseif (!$full) {
                    $direccionDestino .= ' ' . $word;
                } else {
                    $direccionDestino2 .= ' ' . $word;
                }
            }
            if (strlen($direccionDestino2) > 32) {
                $direccionDestino2 = substr($direccionDestino2, 0, 32);
            }
        }

        $params = array(
            'Enseigne' => env('MONDIAL_RELAY_ID'),
            'ModeCol' => $colMode,
            'ModeLiv' => $delivMode,
            'Expe_Langage' => 'ES',
            'Expe_Ad1' => $this->eliminarTildes($envio->configuracionBusiness->usuario->configuracion->nombre),
            'Expe_Ad2' => $this->eliminarTildes($envio->configuracionBusiness->usuario->configuracion->apellidos),
            'Expe_Ad3' => $this->eliminarTildes($direccionOrigen),
            'Expe_Ad4' => $this->eliminarTildes($direccionOrigen2),
            'Expe_Ville' => $this->eliminarTildes($ciudadOrigen),
            'Expe_CP' => $cpOrigen,
            'Expe_Pays' => $paisOrigen,
            'Expe_Tel1' => '+34' . env('TRANSPORTER_BUSINESS_TLF'),
            'Dest_Langage' => $envio->destino->codigoPostal->codigo_pais == 'ES' ? 'ES' : 'EN',
            'Dest_Ad1' => $this->eliminarTildes($envio->destinatario->nombre),
            'Dest_Ad2' => $this->eliminarTildes($envio->destinatario->apellidos),
            'Dest_Ad3' => $this->eliminarTildes($direccionDestino),
            'Dest_Ad4' => $this->eliminarTildes($direccionDestino2),
            'Dest_Ville' => $this->eliminarTildes($ciudadDestino),
            'Dest_CP' => $envio->destino->codigoPostal->codigo_postal,
            'Dest_Pays' => $envio->destino->codigoPostal->codigo_pais,
            'Dest_Tel1' => '+' . $envio->destino->codigoPostal->pais->pref_tlf . $envio->destinatario->telefono,
            'Poids' => $peso * 1000,
            'NbColis' => 1,
            'CRT_Valeur' => 0
        );

        if ($idStoreMROrigen) {
            $params['COL_Rel_Pays'] = $paisOrigen;
            $params['COL_Rel'] = intVal($idStoreMROrigen);
        }

        if ($idStoreMRDestino) {
            $params['LIV_Rel_Pays'] = $paisDestino;
            $params['LIV_Rel'] = intval($idStoreMRDestino);
        }

//        $params['Tavisage'] = 'O';

        $endpoint = Config::get('enums.mondialRelayEndpoints.crearEnvio');

        $response = $this->call($params, $endpoint);
        if ($response['WSI2_CreationExpeditionResult']['STAT'] == 0) {
            $result = $response['WSI2_CreationExpeditionResult'];
            $envioMR = new EnvioMondialRelay();
            $envioMR->envio_business_id = $envio->id;
            $envioMR->num_expedicion = $result['ExpeditionNum'];
            $envioMR->codigo_agencia = $result['TRI_AgenceCode'];
            $envioMR->grupo = $result['TRI_Groupe'];
            $envioMR->navette = $result['TRI_Navette'];
            $envioMR->nombre_agencia = $result['TRI_Agence'];
            $envioMR->tournee = $result['TRI_TourneeCode'];
            $envioMR->modo_entrega = $result['TRI_LivraisonMode'];
            $envioMR->codigo_barras = $result['CodesBarres']['string'];
            $envioMR->save();

            $envio->localizador = '2' . $envioMR->num_expedicion;
            $envio->save();
        } else {
            $error = new ErrorMondialRelay();
            $error->tipo = 'Envio';
            $error->configuracion_business_id = $envio->configuracion_business_id;
            $error->envio_id = $envio->id;
            $error->params = json_encode($params);
            $error->response = json_encode($response);
            $error->save();
            Mail::send('email.mensaje', ['texto' => 'Error en creación de etiqueta'], function ($m) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_SOPORTE'), 'Transporter')->subject('Error de producción');
            });
            return $error;
        }
    }

    public function crearDevolucion($devolucion, $envioDevolucion)
    {

        $envio = $envioDevolucion;

        $colMode = '';
        $delivMode = '';
        $direccionOrigen = '';
        $ciudadOrigen = '';
        $cpOrigen = '';
        $paisOrigen = '';
        $idStoreMROrigen = null;
        $idStoreMRDestino = null;
        $paisDestino = '';

        $ciudadOrigen = $envio->origen->codigoPostal->ciudad;
        if (strlen($ciudadOrigen) > 26) {
            $ciudadOrigen = substr($ciudadOrigen, 0, 26);
        }
        $cpOrigen = $envio->origen->codigoPostal->codigo_postal;
        $paisOrigen = $envio->origen->codigoPostal->codigo_pais;
        $colMode = 'REL';
        if ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE && $envio->origen->store) {
            $direccionOrigen = $envio->origen->store->direccion;
        } elseif ($envio->origen->mondialRelayStore) {
            $direccionOrigen = $envio->origen->mondialRelayStore ?
                $envio->origen->mondialRelayStore->direccion :
                $envio->origen->store->direccion;
            $idStoreMROrigen = $envio->origen->store_id;
        } else {
            $colMode = 'CCC';
            $direccionOrigen = $envio->origen->direccion;
        }

        $paisDestino = $envio->destino->codigoPostal->codigo_pais;
        if ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $delivMode = 'HOM';
            $direccionDestino = $envio->destino->direccion;
        } elseif ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE && $envio->destino->store) {
            $delivMode = 'HOM';
            $direccionDestino = $envio->destino->store->direccion;
        } else {
            $delivMode = '24R';
            $direccionDestino = $envio->destino->mondialRelayStore->direccion;
            $idStoreMRDestino = $envio->destino->store_id;
        }

        $ciudadDestino = $envio->destino->codigoPostal->ciudad;
        if (strlen($ciudadDestino) > 26) {
            $ciudadDestino = substr($ciudadDestino, 0, 26);
        }

        $peso = 0;
        foreach ($devolucion->motivosDevolucionProductos as $motivo) {
            $peso += $motivo->producto->peso;
        }

        if ($peso < 0.15) {
            $peso = 0.15;
        }

        $direccionOrigen2 = '';
        if (strlen($this->eliminarTildes($direccionOrigen)) > 32) {
            $split = explode(' ', $this->eliminarTildes($direccionOrigen));
            $direccionOrigen = '';
            $full = false;
            foreach ($split as $word) {
                if (strlen($direccionOrigen . ' ' . $word) > 32 && !$full) {
                    $full = true;
                    $direccionOrigen2 .= ' ' . $word;
                } elseif (!$full) {
                    $direccionOrigen .= ' ' . $word;
                } else {
                    $direccionOrigen2 .= ' ' . $word;
                }
            }
            if (strlen($direccionOrigen2) > 32) {
                $direccionOrigen2 = substr($direccionOrigen2, 0, 32);
            }
        }

        $direccionDestino2 = '';
        if (strlen($this->eliminarTildes($direccionDestino)) > 32) {
            $split = explode(' ', $this->eliminarTildes($direccionDestino));
            $direccionDestino = '';
            $full = false;
            foreach ($split as $word) {
                if (strlen($direccionDestino . ' ' . $word) > 32 && !$full) {
                    $full = true;
                    $direccionDestino2 .= ' ' . $word;
                } elseif (!$full) {
                    $direccionDestino .= ' ' . $word;
                } else {
                    $direccionDestino2 .= ' ' . $word;
                }
            }
            if (strlen($direccionDestino2) > 32) {
                $direccionDestino2 = substr($direccionDestino2, 0, 32);
            }
        }

        $params = array(
            'Enseigne' => env('MONDIAL_RELAY_ID'),
            'ModeCol' => $colMode,
            'ModeLiv' => $delivMode,

            'Expe_Langage' => $envio->destino->codigoPostal->codigo_pais == 'ES' ? 'ES' : 'EN',
            'Expe_Ad1' => $this->eliminarTildes($envio->destinatario->nombre),
            'Expe_Ad2' => $this->eliminarTildes($envio->destinatario->apellidos),
            'Expe_Ad3' => $this->eliminarTildes($direccionOrigen),
            'Expe_Ad4' => $this->eliminarTildes($direccionOrigen2),
            'Expe_Ville' => $this->eliminarTildes($ciudadOrigen),
            'Expe_CP' => $cpOrigen,
            'Expe_Pays' => $paisOrigen,
            'Expe_Tel1' => '+34' . env('TRANSPORTER_BUSINESS_TLF'),

            'Dest_Langage' => 'ES',
            'Dest_Ad1' => $this->eliminarTildes($envio->configuracionBusiness->usuario->configuracion->nombre),
            'Dest_Ad2' => $this->eliminarTildes($envio->configuracionBusiness->usuario->configuracion->apellidos),
            'Dest_Ad3' => $this->eliminarTildes($direccionDestino),
            'Dest_Ad4' => $this->eliminarTildes($direccionDestino2),
            'Dest_Ville' => $this->eliminarTildes($ciudadDestino),
            'Dest_CP' => $envio->destino->codigoPostal->codigo_postal,
            'Dest_Pays' => $envio->destino->codigoPostal->codigo_pais,
            'Dest_Tel1' => '+34' . $envio->configuracionBusiness->usuario->configuracion->telefono,
            'Poids' => $peso * 1000,
            'NbColis' => 1,
            'CRT_Valeur' => 0
        );

        if ($idStoreMROrigen) {
            $params['COL_Rel_Pays'] = $paisOrigen;
            $params['COL_Rel'] = intVal($idStoreMROrigen);
        }

        if ($idStoreMRDestino && $delivMode == '24R') {
            $params['LIV_Rel_Pays'] = $paisDestino;
            $params['LIV_Rel'] = intval($idStoreMRDestino);
        }

//        $params['Tavisage'] = 'O';

        $endpoint = Config::get('enums.mondialRelayEndpoints.crearEnvio');

        $response = $this->call($params, $endpoint);

        if ($response['WSI2_CreationExpeditionResult']['STAT'] == 0) {
            $result = $response['WSI2_CreationExpeditionResult'];
            $envioMR = new EnvioMondialRelay();
            $envioMR->envio_business_id = $envio->id;
            $envioMR->num_expedicion = $result['ExpeditionNum'];
            $envioMR->codigo_agencia = $result['TRI_AgenceCode'];
            $envioMR->grupo = $result['TRI_Groupe'];
            $envioMR->navette = $result['TRI_Navette'];
            $envioMR->nombre_agencia = $result['TRI_Agence'];
            $envioMR->tournee = $result['TRI_TourneeCode'];
            $envioMR->modo_entrega = $result['TRI_LivraisonMode'];
            $envioMR->codigo_barras = $result['CodesBarres']['string'];
            $envioMR->save();

            $envio->localizador = '2' . $envioMR->num_expedicion;
            $envio->save();
        } else {
            $error = new ErrorMondialRelay();
            $error->tipo = 'Devolucion';
            $error->configuracion_business_id = $envio->configuracion_business_id;
            $error->envio_id = $envio->id;
            $error->params = json_encode($params);
            $error->response = json_encode($response);
            $error->save();
            Mail::send('email.mensaje', ['texto' => 'Error en creación de etiqueta'], function ($m) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_SOPORTE'), 'Transporter')->subject('Error de producción');
            });
            return $error;
        }
    }

    public function crearEnvioDualCarrier($envio)
    {

        $colMode = '';
        $delivMode = '';
        $direccionOrigen = '';
        $ciudadOrigen = '';
        $cpOrigen = '';
        $paisOrigen = '';
        $idStoreMROrigen = null;
        $idStoreMRDestino = null;
        $paisDestino = '';


        if ($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
            $origen = $envio->configuracionBusiness->preferenciaRecogida;
        } else {
            $origen = $envio->origen;
        }
        $ciudadOrigen = $origen->codigoPostal->ciudad;
        if (strlen($ciudadOrigen) > 30) {
            $ciudadOrigen = substr($ciudadOrigen, 0, 30);
        }
        $cpOrigen = $origen->codigoPostal->codigo_postal;
        $paisOrigen = $origen->codigoPostal->codigo_pais;

        if ($origen->tipo_recogida_id == TiposRecogidaBusiness::STORE && $origen->store) {
            $colMode = 'REL';
            $direccionOrigen = $origen->store->direccion;
        } elseif ($origen->mondialRelayStore) {
            $colMode = 'REL';
            $direccionOrigen = $origen->mondialRelayStore ?
                $origen->mondialRelayStore->direccion :
                $origen->store->direccion;
            $idStoreMROrigen = $origen->store_id;
        } else {
            $colMode = 'CCC';
            $direccionOrigen = $origen->direccion;
            $idStoreMROrigen = '';
        }

        $paisDestino = $envio->destino->codigoPostal->codigo_pais;

        if ($paisDestino == 'FR') {
            $delivMode = 'HOC';
        } else {
            $delivMode = 'HOM';
        }

        $ciudadDestino = $envio->destino->codigoPostal->ciudad;
        if (strlen($ciudadDestino) > 30) {
            $ciudadDestino = substr($ciudadDestino, 0, 30);
        }

        $direccionDestino = $envio->destino->direccion;

        $peso = 0;
        foreach ($envio->productos as $producto) {
            $peso += $producto->peso * $producto->pivot->cantidad;
        }

        if ($peso < 0.15) {
            $peso = 0.15;
        }

        if ($idStoreMROrigen) {
            $idStoreMROrigen = $paisOrigen . '-' . $idStoreMROrigen;
        } else {
            $idStoreMROrigen = '';
        }

        // Tratamos direccion origen
        $direccionOrigen = $this->eliminarTildes($direccionOrigen);
        $finalAddressOrigen2 = '';
        if (strlen($direccionOrigen) > 30) {
            $split = explode(' ', $direccionOrigen);
            $finalAddress1 = '';
            foreach ($split as $word) {
                if ($finalAddress1 == '') {
                    $finalAddress1 .= $word;
                } elseif (strlen($finalAddress1 . ' ' . $word) > 30) {
                    break;
                } else {
                    $finalAddress1 .= ' ' . $word;
                }
            }
            $finalAddressOrigen2 = $direccionOrigen;
            $finalAddressOrigen2 = trim(str_replace($finalAddress1, '', $finalAddressOrigen2));
            $direccionOrigen = $finalAddress1;
        }

        // Tratamos direccion destino
        $direccionDestino = $this->eliminarTildes($direccionDestino);
        $finalAddressDestino2 = '';
        if (strlen($direccionDestino) > 30) {
            $split = explode(' ', $direccionDestino);
            $finalAddress1 = '';
            foreach ($split as $word) {
                if ($finalAddress1 == '') {
                    $finalAddress1 .= $word;
                } elseif (strlen($finalAddress1 . ' ' . $word) > 30) {
                    break;
                } else {
                    $finalAddress1 .= ' ' . $word;
                }
            }
            $finalAddressDestino2 = $direccionDestino;
            $finalAddressDestino2 = trim(str_replace($finalAddress1, '', $finalAddressDestino2));
            $direccionDestino = $finalAddress1;
        }

        // Tratamos destinatario
        $destinatario = $this->eliminarTildes($envio->destinatario->nombre . ' ' . $envio->destinatario->apellidos);
        $destinatario2 = '';
        if (strlen($destinatario) > 30) {
            $split = explode(' ', $destinatario);
            $finalDestinatario1 = '';
            foreach ($split as $word) {
                if ($finalDestinatario1 == '') {
                    $finalDestinatario1 .= $word;
                } elseif (strlen($finalDestinatario1 . ' ' . $word) > 30) {
                    break;
                } else {
                    $finalDestinatario1 .= ' ' . $word;
                }
            }
            $destinatario2 = $destinatario;
            $destinatario2 = trim(str_replace($finalDestinatario1, '', $destinatario));
            $destinatario = $finalDestinatario1;
        }

        $params = '<?xml version="1.0" encoding="utf-8"?>' .
            '<ShipmentCreationRequest ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ' .
            'xmlns="http://www.example.org/Request">' .
            '<Context>' .
            '<Login>' . env('MONDIAL_RELAY_DUAL_CARRIER_LOGIN') . '</Login>' .
            '<Password><![CDATA[' . env('MONDIAL_RELAY_DUAL_CARRIER_PASSWORD') . ']]></Password>' .
            '<CustomerId>' . env('MONDIAL_RELAY_DUAL_CARRIER_ID') . '</CustomerId>' .
            '<Culture>es-ES</Culture>' .
            '<VersionAPI>1.0</VersionAPI>' .
            '</Context>' .
            '<OutputOptions>' .
            '<OutputFormat>10x15</OutputFormat>' .
            '<OutputType>PdfUrl</OutputType>' .
            '</OutputOptions>' .
            '<ShipmentsList>' .
            '<Shipment>' .
            '<ParcelCount>1</ParcelCount>' .
            '<DeliveryMode Mode="' . $delivMode . '" Location=""/>' .
            '<CollectionMode Mode="' . $colMode . '" Location="' . $idStoreMROrigen . '"/>' .
            '<Parcels>' .
            '<Parcel>' .
            '<Weight Value="' . $peso * 1000 . '" Unit="gr"/>' .
            '</Parcel>' .
            '</Parcels>' .
            '<Sender>' .
            '<Address>' .
            '<Streetname>' . $this->eliminarTildes($direccionOrigen) . '</Streetname>' .
            '<CountryCode>' . $paisOrigen . '</CountryCode>' .
            '<PostCode>' . $cpOrigen . '</PostCode>' .
            '<AddressAdd1>Transporter</AddressAdd1>' .
            '<AddressAdd3>' . $finalAddressOrigen2 . '</AddressAdd3>' .
            '<City>' . $this->eliminarTildes($ciudadOrigen) . '</City>' .
            '<PhoneNo>+' .
            $envio->destino->codigoPostal->pais->pref_tlf .
            $envio->destinatario->telefono .
            '</PhoneNo>' .
            '</Address>' .
            '</Sender>' .
            '<Recipient>' .
            '<Address>' .
            '<Streetname>' . $this->eliminarTildes($direccionDestino) . '</Streetname>' .
            '<CountryCode>' . $paisDestino . '</CountryCode>' .
            '<PostCode>' . $envio->destino->codigoPostal->codigo_postal . '</PostCode>' .
            '<AddressAdd1>' . ucfirst($destinatario) . '</AddressAdd1>' .
            '<AddressAdd2>' . ucfirst($destinatario2) . '</AddressAdd2>' .
            '<AddressAdd3>' . $finalAddressDestino2 . '</AddressAdd3>' .
            '<City>' . $this->eliminarTildes($ciudadDestino) . '</City>' .
            '<PhoneNo>+' . $envio->destinatario->pais->pref_tlf . $envio->destinatario->telefono . '</PhoneNo>' .
            '</Address>' .
            '</Recipient>' .
            '</Shipment>' .
            '</ShipmentsList>' .
            '</ShipmentCreationRequest>';

        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($params),
            "Connection: close",
            "Accept: application/xml"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('MONDIAL_RELAY_DUAL_CARRIER'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        \Log::info($data);
        \Log::info('Is curl error: ' . curl_errno($ch));
        if (curl_errno($ch)) {
            \Log::error('CURL ERROR: ' . curl_error($ch));
        } else {
            curl_close($ch);
        }

        $parsedData = simplexml_load_string($data);

        if ($parsedData && $parsedData->ShipmentsList) {
            $path = $parsedData->ShipmentsList->Shipment->LabelList->Label->Output->__toString();

            $envio->localizador = '2' . $parsedData->ShipmentsList->Shipment['ShipmentNumber']->__toString();

            EtiquetaDualCarrier::dispatch($envio, $path);

        } else {
            $error = new ErrorMondialRelay();
            $error->tipo = 'Envio dual carrier';
            $error->configuracion_business_id = $envio->configuracion_business_id;
            $error->envio_id = $envio->id;
            $error->params = $params;
            $error->response = $data;
            $error->save();
            Mail::send('email.mensaje', ['texto' => 'Error en creación de etiqueta'], function ($m) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_SOPORTE'), 'Transporter')->subject('Error de producción');
            });
            return $error;
        }
    }

    public function crearDevolucionDualCarrier($envio)
    {

        $devolucion = $envio->devolucionAsDevolucion;

        $colMode = '';
        $delivMode = '';
        $direccionOrigen = '';
        $ciudadOrigen = '';
        $cpOrigen = '';
        $paisOrigen = '';
        $idStoreMROrigen = null;
        $idStoreMRDestino = null;
        $paisDestino = '';

        $ciudadOrigen = $envio->origen->codigoPostal->ciudad;
        if (strlen($ciudadOrigen) > 30) {
            $ciudadOrigen = substr($ciudadOrigen, 0, 30);
        }
        $cpOrigen = $envio->origen->codigoPostal->codigo_postal;
        $paisOrigen = $envio->origen->codigoPostal->codigo_pais;
        $colMode = 'REL';
        if ($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE && $envio->origen->store) {
            $direccionOrigen = $envio->origen->store->direccion;
        } elseif ($envio->origen->mondialRelayStore) {
            $direccionOrigen = $envio->origen->mondialRelayStore ?
                $envio->origen->mondialRelayStore->direccion :
                $envio->origen->store->direccion;
            $idStoreMROrigen = $envio->origen->store_id;
        } else {
            $params = array(
                'Enseigne' => env('MONDIAL_RELAY_ID'),
                'Pays' => $devolucion->envio->destino->codigoPostal->codigo_pais,
                'CP' => $devolucion->envio->destino->codigoPostal->codigo_postal,
                'NombreResultats' => 1,
            );

            $puntoCercano = $this->getPuntoCercano($params);
            $idStoreMROrigen = $puntoCercano->id;
            $direccionOrigen = $envio->origen->direccion;
        }

        $paisDestino = $envio->destino->codigoPostal->codigo_pais;
        if ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $delivMode = 'LCC';
            $direccionDestino = $envio->destino->direccion;
        } elseif ($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE && $envio->destino->store) {
            $delivMode = 'LCC';
            $direccionDestino = $envio->destino->store->direccion;
        } else {
            $delivMode = '24R';
            $direccionDestino = $envio->destino->mondialRelayStore->direccion;
            $idStoreMRDestino = $envio->destino->store_id;
        }

        $ciudadDestino = $envio->destino->codigoPostal->ciudad;
        if (strlen($ciudadDestino) > 30) {
            $ciudadDestino = substr($ciudadDestino, 0, 30);
        }

        $peso = 0;
        foreach ($devolucion->motivosDevolucionProductos as $motivo) {
            $peso += $motivo->producto->peso;
        }

        if ($peso < 0.15) {
            $peso = 0.15;
        }

        if ($idStoreMROrigen) {
            $idStoreMROrigen = $paisOrigen . '-' . $idStoreMROrigen;
        } else {
            $idStoreMROrigen = '';
        }

        if ($idStoreMRDestino) {
            $idStoreMRDestino = $paisDestino . '-' . $idStoreMRDestino;
        } else {
            $idStoreMRDestino = '';
        }

        // Tratamos direccion origen
        $direccionOrigen = $this->eliminarTildes($direccionOrigen);
        $finalAddressOrigen2 = '';
        if (strlen($direccionOrigen) > 30) {
            $split = explode(' ', $direccionOrigen);
            $finalAddress1 = '';
            foreach ($split as $word) {
                if ($finalAddress1 == '') {
                    $finalAddress1 .= $word;
                } elseif (strlen($finalAddress1 . ' ' . $word) > 30) {
                    break;
                } else {
                    $finalAddress1 .= ' ' . $word;
                }
            }
            $finalAddressOrigen2 = $direccionOrigen;
            $finalAddressOrigen2 = trim(str_replace($finalAddress1, '', $finalAddressOrigen2));
            $direccionOrigen = $finalAddress1;
        }

        // Tratamos direccion destino
        $direccionDestino = $this->eliminarTildes($direccionDestino);
        $finalAddressDestino2 = '';
        if (strlen($direccionDestino) > 30) {
            $split = explode(' ', $direccionDestino);
            $finalAddress1 = '';
            foreach ($split as $word) {
                if ($finalAddress1 == '') {
                    $finalAddress1 .= $word;
                } elseif (strlen($finalAddress1 . ' ' . $word) > 30) {
                    break;
                } else {
                    $finalAddress1 .= ' ' . $word;
                }
            }
            $finalAddressDestino2 = $direccionDestino;
            $finalAddressDestino2 = trim(str_replace($finalAddress1, '', $finalAddressDestino2));
            $direccionDestino = $finalAddress1;
        }

        $params = '<?xml version="1.0" encoding="utf-8"?>' .
            '<ShipmentCreationRequest ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ' .
            'xmlns="http://www.example.org/Request">' .
            '<Context>' .
            '<Login>' . env('MONDIAL_RELAY_DUAL_CARRIER_LOGIN') . '</Login>' .
            '<Password><![CDATA[' . env('MONDIAL_RELAY_DUAL_CARRIER_PASSWORD') . ']]></Password>' .
            '<CustomerId>' . env('MONDIAL_RELAY_DUAL_CARRIER_ID') . '</CustomerId>' .
            '<Culture>es-ES</Culture>' .
            '<VersionAPI>1.0</VersionAPI>' .
            '</Context>' .
            '<OutputOptions>' .
            '<OutputFormat>10x15</OutputFormat>' .
            '<OutputType>PdfUrl</OutputType>' .
            '</OutputOptions>' .
            '<ShipmentsList>' .
            '<Shipment>' .
            '<ParcelCount>1</ParcelCount>' .
            '<DeliveryMode Mode="' . $delivMode . '" Location="' . $idStoreMRDestino . '"/>' .
            '<CollectionMode Mode="' . $colMode . '" Location="' . $idStoreMROrigen . '"/>' .
            '<Parcels>' .
            '<Parcel>' .
            '<Weight Value="' . $peso * 1000 . '" Unit="gr"/>' .
            '</Parcel>' .
            '</Parcels>' .
            '<Sender>' .
            '<Address>' .
            '<Streetname>' . $this->eliminarTildes($direccionOrigen) . '</Streetname>' .
            '<CountryCode>' . $paisOrigen . '</CountryCode>' .
            '<PostCode>' . $cpOrigen . '</PostCode>' .
            '<AddressAdd3>' . $finalAddressOrigen2 . '</AddressAdd3>' .
            '<City>' . $this->eliminarTildes($ciudadOrigen) . '</City>' .
            '<PhoneNo>+' .
            $devolucion->envio->destino->codigoPostal->pais->pref_tlf .
            $devolucion->envio->destinatario->telefono .
            '</PhoneNo>' .
            '</Address>' .
            '</Sender>' .
            '<Recipient>' .
            '<Address>' .
            '<Streetname>' . $this->eliminarTildes($direccionDestino) . '</Streetname>' .
            '<CountryCode>' . $paisDestino . '</CountryCode>' .
            '<PostCode>' . $envio->destino->codigoPostal->codigo_postal . '</PostCode>' .
            '<AddressAdd1>' .
            ucfirst($envio->configuracionBusiness->usuario->configuracion->nombre) .
            ' ' .
            ucfirst($envio->configuracionBusiness->usuario->configuracion->apellidos) .
            '</AddressAdd1>' .
            '<AddressAdd3>' . $finalAddressDestino2 . '</AddressAdd3>' .
            '<City>' . $this->eliminarTildes($ciudadDestino) . '</City>' .
            '<PhoneNo>+34' . $envio->configuracionBusiness->usuario->configuracion->telefono . '</PhoneNo>' .
            '</Address>' .
            '</Recipient>' .
            '</Shipment>' .
            '</ShipmentsList>' .
            '</ShipmentCreationRequest>';

        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($params),
            "Connection: close",
            "Accept: application/xml"
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('MONDIAL_RELAY_DUAL_CARRIER'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        \Log::info('Respuesta devolucion dual carrier: ' . $data);
        \Log::info('Is curl error: ' . curl_errno($ch));
        if (curl_errno($ch)) {
            \Log::error('CURL ERROR: ' . curl_error($ch));
        } else {
            curl_close($ch);
        }

        $parsedData = simplexml_load_string($data);

        if ($parsedData && $parsedData->ShipmentsList) {
            $path = $parsedData->ShipmentsList->Shipment->LabelList->Label->Output->__toString();

            $envio->localizador = '2' . $parsedData->ShipmentsList->Shipment['ShipmentNumber']->__toString();

            EtiquetaDualCarrier::dispatch($envio, $path);
        } else {
            $error = new ErrorMondialRelay();
            $error->tipo = 'Devolucion dual carrier';
            $error->configuracion_business_id = $envio->configuracion_business_id;
            $error->envio_id = $envio->id;
            $error->params = $params;
            $error->response = $data;
            $error->save();
            Mail::send('email.mensaje', ['texto' => 'Error en creación de etiqueta'], function ($m) {
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_SOPORTE'), 'Transporter')->subject('Error de producción');
            });
            return $error;
        }
    }

    private function eliminarTildes($str)
    {
        $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A',
            'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a',
            'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');

        $sinTildes = preg_replace('/[^A-Za-z0-9\ ]/', ' ', strtr($str, $unwanted_array));

        return trim(preg_replace('/\s+/', ' ', $sinTildes));
    }
}
