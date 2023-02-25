<?php

namespace App\Services;

use App\Models\Punto;
use App\Models\TiposRecogidaBusiness;
use Geocoder;

class PreferenciaRecogidaService
{

    protected $mondialRelayService;

    /**
     * Create a new controller instance.
     */
    public function __construct(MondialRelayService $mondialRelayService) {
        $this->mondialRelayService = $mondialRelayService;
    }


    public function populateRecogida($preferenciaRecogida)
    {

        if($preferenciaRecogida->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {

            $domicilio = new \stdClass();
            $addres = Geocoder::geocode($preferenciaRecogida->direccion . ' ' . $preferenciaRecogida->codigoPostal->codigo_postal . ' ' . $preferenciaRecogida->codigoPostal->ciudad . ' ' . $preferenciaRecogida->codigoPostal->codigo_pais)->get()->first();
            $domicilio->latitud = $addres->getCoordinates()->getLatitude();
            $domicilio->longitud = $addres->getCoordinates()->getLongitude();
            $preferenciaRecogida->domicilio = $domicilio;

        } else if($preferenciaRecogida->tipo_recogida_id == TiposRecogidaBusiness::STORE) {

            if($preferenciaRecogida->tipo_store_id == \Config::get('enums.tiposStores.puntoPack')) {
                $params = array(
                    'Enseigne' => env('MONDIAL_RELAY_ID'),
                    'Pays' => $preferenciaRecogida->codigoPostal->codigo_pais,
                    'NumPointRelais' => $preferenciaRecogida->store_id,
                );

                $preferenciaRecogida->store = $this->mondialRelayService->getPunto($params);

            } else if($preferenciaRecogida->tipo_store_id == \Config::get('enums.tiposStores.transporter')) {
                $preferenciaRecogida->store = Punto::find($preferenciaRecogida->store_id);
            }

        }

        return $preferenciaRecogida;

    }
}
