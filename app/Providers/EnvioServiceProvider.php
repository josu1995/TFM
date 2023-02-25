<?php

namespace App\Providers;

use App\Models\EnvioBusiness;
use App\Models\Pais;
use App\Models\PaqueteBusiness;
use App\Models\PedidoBusiness;
use App\Models\TiposRecogidaBusiness;
use Illuminate\Support\ServiceProvider;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Session;

class EnvioServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    
    public function boot()
    {

        Validator::extend('max_medidas', function ($attribute, $value, $parameters, $validator) {

            if ($value > 120) {
                return false;
            }

            return true;
        });

        Validator::extend('max_medidas_array', function ($attribute, $value, $parameters, $validator) {

            $splitAttr = explode('.', $attribute);
            //comprobamos que el formato sea el correcto
            $data = $validator->getData()[$splitAttr[0]];

            if ($data['tipo_recogida'] == 'D' && $data['tipo_entrega'] == 'D' && $data['pais_destino'] == 'ES'
            ) {
                return true;
            } elseif ($value > 120) {
                return false;
            }

            return true;
        });

        Validator::extend('max_medidas_edit', function ($attribute, $value, $parameters, $validator) {

            if ($attribute == 'embalaje_edit') {
                $embalaje = PaqueteBusiness::find($value);

                if ($embalaje->largo > 120 || $embalaje->alto > 120 || $embalaje->ancho > 120) {
                    return false;
                }
            } else {
                if ($value > 120) {
                    return false;
                }
            }

            return true;
        });

        Validator::extend('sum_medidas_store_edit', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('largo', $data) || !array_key_exists('alto', $data) || !array_key_exists('ancho', $data)) {
                return true;
            }

            if (intval(str_replace(',', '.', $data['largo'])) + intval(str_replace(',', '.', $data['alto'])) + intval(str_replace(',', '.', $data['ancho'])) > 150) {
                return false;
            }

            return true;
        });

        Validator::extend('sum_medidas_domicilio_edit', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('largo', $data) || !array_key_exists('alto', $data) || !array_key_exists('ancho', $data)) {
                return true;
            }

            if (intval(str_replace(',', '.', $data['largo'])) + intval(str_replace(',', '.', $data['alto'])) + intval(str_replace(',', '.', $data['ancho'])) > 250) {
                return false;
            }

            return true;
        });


        Validator::extend('sum_medidas_store', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('largo', $data)
                || !array_key_exists('alto', $data)
                || !array_key_exists('ancho', $data)) {
                return true;
            }

            if (!$data['tipo_recogida_id']) {
                $preferencia = \Auth::guard('business')->user()->configuracionBusiness->preferenciaRecogida;

                if ($preferencia
                    && $preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO
                    && $data['tipo_entrega_destino_id'] == TiposRecogidaBusiness::DOMICILIO
                    && Pais::find($data['pais_destino_id'])->iso2 == 'ES') {
                    return true;
                } elseif (intval(str_replace(',', '.', $data['largo']))
                    + intval(str_replace(',', '.', $data['alto']))
                    + intval(str_replace(',', '.', $data['ancho'])) > 150) {
                    return false;
                }
            } else {
                if ($data['tipo_recogida_id'] == TiposRecogidaBusiness::DOMICILIO
                    && $data['tipo_entrega_destino_id'] == TiposRecogidaBusiness::DOMICILIO
                    && Pais::find($data['pais_destino_id'])->iso2 == 'ES') {
                    return true;
                } elseif (intval(str_replace(',', '.', $data['largo']))
                    + intval(str_replace(',', '.', $data['alto']))
                    + intval(str_replace(',', '.', $data['ancho'])) > 150) {
                    return false;
                }
            }

            return true;
        });

        Validator::extend('sum_medidas_store_api', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('package', $data) || !array_key_exists('destination', $data)) {
                return true;
            }

            $package = $data['package'];
            $destination = $data['destination'];

            if (!array_key_exists('depth', $package)
                || !array_key_exists('height', $package)
                || !array_key_exists('width', $package)
                || !array_key_exists('country', $destination)) {
                return true;
            }

            if (array_key_exists('storeId', $destination)
                || $parameters[0] == TiposRecogidaBusiness::STORE
                || $destination['country'] != 'ES') {
                if (intval(str_replace(',', '.', $package['depth']))
                    + intval(str_replace(',', '.', $package['height']))
                    + intval(str_replace(',', '.', $package['width'])) > 150) {
                    return false;
                }
            }

            return true;
        });

        Validator::extend('sum_medidas_domicilio', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('largo', $data)
                || !array_key_exists('alto', $data)
                || !array_key_exists('ancho', $data)) {
                return true;
            }

            if (!$data['tipo_recogida_id']) {
                $preferencia = \Auth::guard('business')->user()->configuracionBusiness->preferenciaRecogida;

                if ($preferencia
                    && $preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO
                    && $data['tipo_entrega_destino_id'] == TiposRecogidaBusiness::DOMICILIO
                    && Pais::find($data['pais_destino_id'])->iso2 == 'ES') {
                    if (intval(str_replace(',', '.', $data['largo']))
                        + intval(str_replace(',', '.', $data['alto']))
                        + intval(str_replace(',', '.', $data['ancho'])) > 250) {
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                if ($data['tipo_recogida_id'] == TiposRecogidaBusiness::DOMICILIO
                    && $data['tipo_entrega_destino_id'] == TiposRecogidaBusiness::DOMICILIO
                    && Pais::find($data['pais_destino_id'])->iso2 == 'ES') {
                    if (intval(str_replace(',', '.', $data['largo']))
                        + intval(str_replace(',', '.', $data['alto']))
                        + intval(str_replace(',', '.', $data['ancho'])) > 250) {
                        return false;
                    }
                } else {
                    return true;
                }
            }

            return true;
        });

        Validator::extend('sum_medidas_domicilio_api', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (!array_key_exists('package', $data) || !array_key_exists('destination', $data)) {
                return true;
            }

            $package = $data['package'];
            $destination = $data['destination'];

            if (!array_key_exists('depth', $package)
                || !array_key_exists('height', $package)
                || !array_key_exists('width', $package)
                || !array_key_exists('country', $destination)
                || array_key_exists('storeId', $destination)
                || $parameters[0] == TiposRecogidaBusiness::STORE
                || $destination['country'] != 'ES') {
                return true;
            }

            if (intval(str_replace(',', '.', $package['depth']))
                + intval(str_replace(',', '.', $package['height']))
                + intval(str_replace(',', '.', $package['width'])) > 250) {
                return false;
            }

            return true;
        });

        Validator::extend('sum_medidas', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $data = $validator->getData();

            if (intval(str_replace(',', '.', $data['largo'])) + intval(str_replace(',', '.', $data['alto'])) + intval(str_replace(',', '.', $data['ancho'])) > 240) {
                return false;
            }

            return true;
        });

        Validator::extend('sum_medidas_dom_es_array', function ($attribute, $value, $parameters, $validator) {

            $splitAttr = explode('.', $attribute);
            //comprobamos que el formato sea el correcto
            $data = $validator->getData()[$splitAttr[0]];
            if ($data['tipo_recogida'] == 'D' && $data['tipo_entrega'] == 'D' && $data['pais_destino'] == 'ES') {
                if (intval(str_replace(',', '.', $data['largo'])) + intval(str_replace(',', '.', $data['alto'])) + intval(str_replace(',', '.', $data['ancho'])) > 250) {
                    return false;
                }
            } else {
                return true;
            }


            return true;
        });
        
        Validator::extend('sum_medidas_array', function ($attribute, $value, $parameters, $validator) {
            
            $splitAttr = explode('.', $attribute);

            $data = $validator->getData()[$splitAttr[0]];
            $validacion = true;
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
           
            
            if(Arr::has($data[$index], 'tipo_recogida') && Arr::has($data[$index], 'tipo_entrega') && Arr::has($data[$index], 'pais_destino')){
                if ($data['tipo_recogida'] == 'D' && $data['tipo_entrega'] == 'D' && $data['pais_destino'] == 'ES') {
                    $validacion = true;
                } else {
                    if (intval(str_replace(',', '.', $data[$index]['largo'])) + intval(str_replace(',', '.', $data[$index]['alto'])) + intval(str_replace(',', '.', $data[$index]['ancho'])) > 240) {
                        $validacion = false;
                            
                    }
                }

            }else{
                if(Arr::has($data[$index], 'largo') && Arr::has($data[$index], 'alto') && Arr::has($data[$index], 'ancho')){
                    if (intval(str_replace(',', '.', $data[$index]['largo'])) + intval(str_replace(',', '.', $data[$index]['alto'])) + intval(str_replace(',', '.', $data[$index]['ancho'])) > 240) {
                        $validacion = false;
                            
                    }
                }else{
                    $validacion = false;
                }
            }


            return $validacion;
        });

        
        Validator::extend('predeterminado_count', function ($attribute, $value, $parameters, $validator) {
            $splitAttr = explode('.', $attribute);

            $data = $validator->getData()[$splitAttr[0]];
            $validacion = true;
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if(Arr::has($data[$index], 'predeterminado')){
                if($data[$index]['predeterminado'] == 'S'){
                    if(Session::get('predeterminado') == 'S'){
                        if(Session::get('contPredeterminado') == 1){
                            return false;
                        }
                    }else{
                        Session::put('predeterminado','S');
                        Session::put('contPredeterminado',1);
                        return true;
                    }
                    
                }else{
                    return true;
                }
            }else{
                return false;
            }
            
        });

        Validator::extend('comprobar_alto', function ($attribute, $value, $parameters, $validator) {
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'alto')){
                LOg::info('entra1',array(intval(str_replace(',', '.', $data[$index]['alto']))));
                if (intval(str_replace(',', '.', $data[$index]['alto'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_ancho', function ($attribute, $value, $parameters, $validator) {
           
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'ancho')){
            
                if ((int)intval(str_replace(',', '.', $data[$index]['ancho'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_largo', function ($attribute, $value, $parameters, $validator) {
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'largo')){
                if (intval(str_replace(',', '.', $data[$index]['largo'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });

        Validator::extend('comprobar_alto_max', function ($attribute, $value, $parameters, $validator) {
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'alto')){
                LOg::info('entra1',array(intval(str_replace(',', '.', $data[$index]['alto']))));
                if (intval(str_replace(',', '.', $data[$index]['alto'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_ancho_max', function ($attribute, $value, $parameters, $validator) {
           
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'ancho')){
            
                if ((int)intval(str_replace(',', '.', $data[$index]['ancho'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_largo_max', function ($attribute, $value, $parameters, $validator) {
            $splitAttr = explode('.', $attribute);
            $data = $validator->getData()[$splitAttr[0]];
            $exploded = explode('.', $attribute);
            $nombre = $exploded[0];
            $index = $exploded[1];
            if( Arr::has($data[$index], 'largo')){
                if (intval(str_replace(',', '.', $data[$index]['largo'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });

        // //

        Validator::extend('comprobar_alto1', function ($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();

            if( Arr::has($data, 'alto')){
                
                if (intval(str_replace(',', '.', $data['alto'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_ancho1', function ($attribute, $value, $parameters, $validator) {
           
        
            $data = $validator->getData();

            if( Arr::has($data, 'ancho')){
            
                if ((int)intval(str_replace(',', '.', $data['ancho'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_largo1', function ($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();

            if( Arr::has($data, 'largo')){
                if (intval(str_replace(',', '.', $data['largo'])) == 0){
                    return false;
                }else{
                    return true;
                }
            }
        });

        Validator::extend('comprobar_alto_max1', function ($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();
           
            if( Arr::has($data, 'alto')){
     
                if (intval(str_replace(',', '.', $data['alto'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_ancho_max1', function ($attribute, $value, $parameters, $validator) {
           
    
            $data = $validator->getData();

            if( Arr::has($data, 'ancho')){
            
                if ((int)intval(str_replace(',', '.', $data['ancho'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });
        Validator::extend('comprobar_largo_max1', function ($attribute, $value, $parameters, $validator) {
 
            $data = $validator->getData();

            if( Arr::has($data, 'largo')){
                if (intval(str_replace(',', '.', $data['largo'])) > 150){
                    return false;
                }else{
                    return true;
                }
            }
        });

        // //

        Validator::extend('sum_pesos', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $exploded = explode('.', $attribute);
            $data = $validator->getData();
            if (count($exploded) == 2) {
                $nombre = $exploded[0];
                $index = $exploded[1];
                $peso = 0;
                foreach ($data[$nombre] as $key => $curPeso) {
                    $peso += $curPeso * $data[$parameters[1]][$key];
                }
            } elseif (count($exploded) == 3) {
                $nombre = $exploded[0];
                $index = $exploded[1];
                $attr = $exploded[1];
                $peso = 0;
                foreach ($data[$nombre] as $curObj) {
                    if (!isset($curObj[$parameters[1]]) || !isset($curObj[$parameters[2]])) {
                        return true;
                    }
                    $peso += $curObj[$parameters[1]] * $curObj[$parameters[2]];
                }
            }
            if ($peso > 20 && $index == $parameters[0]) {
                return false;
            }
            return true;
        });

        Validator::extend('sum_pesos_min', function ($attribute, $value, $parameters, $validator) {

            //comprobamos que el formato sea el correcto
            $exploded = explode('.', $attribute);
            $data = $validator->getData();
            if (count($exploded) == 2) {
                $nombre = $exploded[0];
                $index = $exploded[1];
                $peso = 0;
                foreach ($data[$nombre] as $key => $curPeso) {
                    $peso += $curPeso * $data[$parameters[1]][$key];
                }
            } elseif (count($exploded) == 3) {
                $nombre = $exploded[0];
                $index = $exploded[1];
                $attr = $exploded[1];
                $peso = 0;
                foreach ($data[$nombre] as $curObj) {
                    if (!isset($curObj[$parameters[1]]) || !isset($curObj[$parameters[2]])) {
                        return true;
                    }
                    $peso += $curObj[$parameters[1]] * $curObj[$parameters[2]];
                }
            }
            if ($peso < 0.10 && $index == $parameters[0]) {
                return false;
            }
            return true;
        });

        Validator::extend('uniqueOrderId', function ($attribute, $value, $parameters, $validator) {

            $pedido = PedidoBusiness::where([
                ['num_pedido', $value],
                ['configuracion_business_id', $parameters[0]]
            ])->first();

            if ($pedido) {
                $envios = EnvioBusiness::where('pedido_id', $pedido->id)->whereNull('deleted_at')->get();
                if (count($envios)) {
                    return false;
                }
            }

            return true;
        });


        Validator::extend('comprobar_condicion_regla',function($attribute,$value,$parameters,$validator){
            Log::info('Nuse',array($value));
            if(is_null($value['primero']) || $value['primero'] == '1'){
                return false;
            }else{
                if(is_null($value['segundo']) || $value['segundo'] == '2'){
                    return false;
                }else{
                    if(!$value['tercero']){
                        return false;
                    }else{
                        if(is_null($value['tercero'][0])){
                            return false;
                        }else{
                            if($value['primero'] == 'Peso expedición (kg)' || $value['primero'] == 'Valor expedición (€)' | $value['primero'] == 'Nº de bultos' | $value['primero'] == 'Nº de productos'){
                                if($value['segundo'] == 'Entre'){
                                    if (intval(str_replace(',', '.', $value['tercero'][0])) < 0 && intval(str_replace(',', '.', $value['tercero'][1])) < 0){
                                        return false;
                                    }else{
                                        if (intval(str_replace(',', '.', $value['tercero'][0])) < intval(str_replace(',', '.', $value['tercero'][1]))){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }
                                }else{
                                    if (intval(str_replace(',', '.', $value['tercero'])) < 0){
                                        return false;
                                    }else{
                                        return true;
                                    }
                                }
                            }else{
                                return true;
                            } 
                        }
                    }
                }
            }

           
        });

        Validator::extend('comprobar_accion_regla',function($attribute,$value,$parameters,$validator){
            

            if(is_null($value['primero']) || $value['primero'] == '1'){
                return false;
            }else{
                if($value['primero'] == 'Asegurar con'){
                    if(is_null($value['segundo']) || $value['segundo'] == '2'){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    if(is_null($value['segundo']) || $value['segundo'] == '2'){
                        return false;
                    }else{
                        if(is_null($value['tercero'])){
                            return false;
                        }else{
                            return true;
                        }
                    }
                }
            }



            
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EnvioService::class, function ($app) {
            return new EnvioService();
        });
    }
}
