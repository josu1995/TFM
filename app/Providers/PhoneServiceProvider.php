<?php

namespace App\Providers;

use App\Models\Pais;
use Illuminate\Support\ServiceProvider;
use Validator;

class PhoneServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('business_phone', function($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();
            $paisId = $data[$parameters[0]];
            $pais = Pais::find($paisId);

            switch ($pais->iso2) {
                case 'DE':
                    return preg_match_all("/^[0-9]{5,11}$/", $value);
                    break;
                case 'AT':
                    return preg_match_all("/^[0-9]{4,13}$/", $value);
                    break;
                case 'BE':
                    return preg_match_all("/^[0-9]{8}$/", $value) || preg_match_all("/^[4]?[0-9]{8}$/", $value);
                    break;
                case 'ES':
                    return preg_match_all("/^[1-9][0-9]{8}$/", $value);
                    break;
                case 'FR':
                    return preg_match_all("/^[1-9][0-9]{8}$/", $value);
                    break;
                case 'LU':
                    return preg_match_all("/^[0-9]{5,9}$/", $value);
                    break;
                case 'NL':
                    return preg_match_all("/^[0-9]{9}$/", $value);
                    break;
                case 'GB':
                    return preg_match_all("/^[0-9]{7,10}$/", $value);
                    break;
            }
            return false;
        });

        Validator::extend('business_api_phone', function($attribute, $value, $parameters, $validator) {

            $pais = $parameters[0];

            switch ($pais) {
                case 'DE':
                    return preg_match_all("/^[0-9]{5,11}$/", $value);
                    break;
                case 'AT':
                    return preg_match_all("/^[0-9]{4,13}$/", $value);
                    break;
                case 'BE':
                    return preg_match_all("/^[0-9]{8}$/", $value) || preg_match_all("/^[4]?[0-9]{8}$/", $value);
                    break;
                case 'ES':
                    return preg_match_all("/^[1-9][0-9]{8}$/", $value);
                    break;
                case 'FR':
                    return preg_match_all("/^[1-9][0-9]{8}$/", $value);
                    break;
                case 'LU':
                    return preg_match_all("/^[0-9]{5,9}$/", $value);
                    break;
                case 'NL':
                    return preg_match_all("/^[0-9]{9}$/", $value);
                    break;
                case 'GB':
                    return preg_match_all("/^[0-9]{7,10}$/", $value);
                    break;
            }
            return true;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
