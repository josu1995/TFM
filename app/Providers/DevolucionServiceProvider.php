<?php

namespace App\Providers;

use App\Models\MotivoDevolucionBusiness;
use Illuminate\Support\ServiceProvider;
use Validator;

class DevolucionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('motivo', function($attribute, $value, $parameters, $validator) {

            $checked = array_key_exists('checked', $validator->getData()[explode('.', $attribute)[0]]);

            if(($checked && $value != null && MotivoDevolucionBusiness::where('id', $value)->count()) || !$checked) {
                return true;
            }

            return false;
        });

        Validator::extend('devolucion_descripcion', function($attribute, $value, $parameters, $validator) {

            $checked = array_key_exists('checked', $validator->getData()[explode('.', $attribute)[0]]);

            $motivo = $validator->getData()[explode('.', $attribute)[0]]['motivo'];

            if(($checked && ($motivo == MotivoDevolucionBusiness::PROD_EMBALAJE_DANADOS || $motivo == MotivoDevolucionBusiness::PROD_DANADO || $motivo == MotivoDevolucionBusiness::PROD_INCOMPLETO || MotivoDevolucionBusiness::OTROS) && $value != null) || !$checked) {
                return true;
            }

            return false;
        });

        Validator::extendImplicit('devolucion_imagen', function($attribute, $value, $parameters, $validator) {

            $checked = array_key_exists('checked', $validator->getData()[explode('.', $attribute)[0]]);

            $motivo = $validator->getData()[explode('.', $attribute)[0]]['motivo'];

            if(($checked && ($motivo == MotivoDevolucionBusiness::PROD_EMBALAJE_DANADOS || $motivo == MotivoDevolucionBusiness::PROD_DANADO)) || !$checked || ($motivo != MotivoDevolucionBusiness::PROD_EMBALAJE_DANADOS && $motivo != MotivoDevolucionBusiness::PROD_DANADO)) {
                return true;
            }

            return false;
        });

        Validator::extend('devolucion_opcion', function($attribute, $value, $parameters, $validator) {

            $checked = array_key_exists('checked', $validator->getData()[explode('.', $attribute)[0]]);

            $motivo = $validator->getData()[explode('.', $attribute)[0]]['motivo'];

            if(($checked && $motivo == MotivoDevolucionBusiness::DESCRIPCION_INSUFICIENTE && $value != null) || !$checked) {
                return true;
            }

            return false;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
