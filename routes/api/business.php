<?php

/*
|--------------------------------------------------------------------------
| API TBUSINESS
|--------------------------------------------------------------------------
*/

// Versión 1
Route::group(['prefix' => 'v1'], function () {

    // Configuracion
    Route::group(['prefix' => 'configuracion'], function () {

        Route::get('/validate', [
            'as' => 'api_business_validate_token',
            'uses' => 'ConfiguracionController@validarTokenApi'
        ]);

        Route::group(['prefix' => '/stores', 'as' => 'config_'], function () {
            Route::put('/{store}', ['as' => 'update_store', 'uses' => 'StoresController@editStore']);
        });

        Route::get('/direccion-recogida', [
            'as' => 'api_business_direccion_recogida',
            'uses' => 'ConfiguracionController@getDireccionRecogida'
        ]);
        Route::get('/estados', ['as' => 'api_business_estados', 'uses' => 'ConfiguracionController@getEstados']);
        Route::get('/transportistas', [
            'as' => 'api_business_transportistas',
            'uses' => 'ConfiguracionController@getTransportistas'
        ]);
    });

    Route::get('/stores', [
        'as' => 'api_business_stores',
        'uses' => 'StoresController@getStores'
    ]);

    Route::group(['prefix' => 'shipments'], function () {
        Route::post('/', ['as' => 'api_business_post_shipment', 'uses' => 'EnvioController@postEnvio']);
        Route::get('/states', [
            'as' => 'api_business_get_shipments_states',
            'uses' => 'EnvioController@getEstados'
        ]);
        Route::get('/{id}/state', [
            'as' => 'api_business_get_shipment_state',
            'uses' => 'EnvioController@getEstadoEnvio'
        ]);
    });
});
