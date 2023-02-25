<?php

use App\Models\AlmacenEntregasECommerce;
use Illuminate\Support\Arr;

use App\Models\Estado;
use App\Models\CodigoPostal;

/**
 * Comprobar si un viaje esta finalizado con datos recibidos por array
 *
 * @return bool
 */
if (!function_exists('isFinalizado')) {
    function isFinalizado($envios)
    {
        $finalizado = true;
        foreach ($envios as $envio) {
            if ($envio->estado_id != Estado::FINALIZADO) {
                return false;
            }
        }
        return $finalizado;
    }
}

/**
 * Check whether the city is already in use or not.
 *
 * @return bool
 */
if (!function_exists('isQCommerceDeliveryCityAlreadyInUse')) {
    function isQCommerceDeliveryCityAlreadyInUse($configBizID, $zipCodeID)
    {
        $qCommerceCities = CodigoPostal::with(['municipios'])->whereHas(
            'almacenesRecogida',
            fn ($qry) => ($qry
                ->where('configuracion_business_id', $configBizID)
                ->where('activo', true)
                ->whereHas(
                    'entregasQCommerce',
                    fn ($innerQry) => ($innerQry->where('es_almacen_delivery', true))
                )
            )
        )->get();

        $qCommerceCities = collect(Arr::flatten($qCommerceCities->map(
            fn ($city) => $city->municipios->pluck('municipio')->toArray()
        )));

        $newCity = CodigoPostal::with(['municipios'])->where('id', $zipCodeID)->first();

        return !is_null($newCity->municipios->pluck('municipio')->first(fn ($value) => $qCommerceCities->contains($value)));
    }
}

if (!function_exists('hasECommerceStores')) {
    function hasECommerceStores($configBizID)
    {
        return AlmacenEntregasECommerce::whereHas(
            'almacenRecogida',
            function ($qry) use ($configBizID) {
                $qry
                    ->where('configuracion_business_id', '=', $configBizID)
                    ->where('activo', '=', 1);
            }
        )->count() > 0;
    }
}
