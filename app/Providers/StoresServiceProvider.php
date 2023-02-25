<?php

namespace App\Providers;

use App\Models\Pais;
use App\Models\Punto;
use App\Services\MondialRelayService;
use Validator;
use Illuminate\Support\ServiceProvider;

class StoresServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {

        Validator::extend('id_store_exists', function ($attribute, $value, $parameters, $validator) {

            $firstVal = substr($value, 0, 1);

            if ($firstVal == \Config::get('enums.tiposStores.puntoPack')) {
                $mondialRelayService = new MondialRelayService();

                $id = substr($value, 1);
                $params = array(
                    'Enseigne' => env('MONDIAL_RELAY_ID'),
                    'Pays' => $parameters[0],
                    'NumPointRelais' => $id,
                );

                $punto = $mondialRelayService->getPunto($params);

                if ($punto) {
                    return true;
                }

            } elseif ($firstVal == \Config::get('enums.tiposStores.transporter')) {
                $id = substr($value, strlen($value) - 2);
                $punto = Punto::find($id);

                if ($punto) {
                    return true;
                }
            }
            return false;
        });

        Validator::extend('store_exists', function ($attribute, $value, $parameters, $validator) {

            $firstVal = substr($value, 0, 1);

            if ($firstVal == \Config::get('enums.tiposStores.puntoPack')) {
                $mondialRelayService = new MondialRelayService();

                $id = substr($value, 1);
                if (strpos($parameters[0], 'origen') !== false) {
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => 'ES',
                        'NumPointRelais' => $id,
                    );
                } else {
                    $data = $validator->getData();
                    $pais = Pais::find($data[$parameters[1]]);
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => $pais->iso2,
                        'NumPointRelais' => $id,
                    );
                }

                $punto = $mondialRelayService->getPunto($params);

                if ($punto) {
                    return true;
                }

            } elseif ($firstVal == \Config::get('enums.tiposStores.transporter')) {
                $id = substr($value, strlen($value) - 2);
                $punto = Punto::find($id);

                if ($punto) {
                    return true;
                }
            }
            return false;
        });

        Validator::extend('store_exists_array', function ($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();
            $index = explode('.', $attribute)[0];
            $firstVal = substr($value, 0, 1);

            if ($firstVal == \Config::get('enums.tiposStores.puntoPack')) {
                $mondialRelayService = new MondialRelayService();

                $id = substr($value, 1);
                if (strpos($parameters[0], 'origen') !== false) {
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => 'ES',
                        'NumPointRelais' => $id,
                    );
                } else {
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => $data[$index][$parameters[1]],
                        'NumPointRelais' => $id,
                    );
                }

                $punto = $mondialRelayService->getPunto($params);

                if ($punto) {
                    return true;
                }

            } elseif ($firstVal == \Config::get('enums.tiposStores.transporter')) {
                $id = substr($value, strlen($value) - 2);
                $punto = Punto::find($id);

                if ($punto) {
                    return true;
                }
            } else {
                if ($parameters[0] == 'origen') {
                    return $data[$index]['tipo_recogida'] == 'D';
                } else {
                    return $data[$index]['tipo_entrega'] == 'D';
                }
            }
            return false;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
