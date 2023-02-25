<?php

/*
|--------------------------------------------------------------------------
| API TDRIVER
|--------------------------------------------------------------------------
*/

// Versión 1
Route::group(['prefix' => 'v1'], function () {

    // Version
    Route::get('/version', ['as' => 'api_tdriver_version', 'uses' => 'LoginController@getVersion']);

    // Auth
    Route::post('/registro/validar', ['as' => 'api_tdriver_validar_registro', 'uses' => 'LoginController@validarRegistro']);
    Route::post('/registro/particular/validar', ['as' => 'api_tdriver_validar_registro_particular', 'uses' => 'LoginController@validarRegistroParticular']);
    Route::post('/login', ['as' => 'api_tdriver_login', 'uses' => 'LoginController@login']);
    Route::post('/login/validar', ['as' => 'api_tdriver_validar_login', 'uses' => 'LoginController@validarLogin']);
    Route::post('/logout', ['as' => 'api_tdriver_logout', 'uses' => 'LoginController@logout']);
    Route::post('/password/reset', ['as' => 'api_tdriver_reset',  'uses' => '\App\Http\Controllers\Web\Auth\ForgotPasswordController@sendResetLinkEmail']);

    // FCM TOKEN
    Route::post('/users/{userId}/subscribe', ['as' => 'api_tdriver_fcm', 'uses' => 'LoginController@subscribe']);

    // INICIO
    Route::get('/users/{userId}/home', ['as' => 'api_tdriver_home', 'uses' => 'HomeController@getHomeData']);

    // VIAJAR
    Route::get('/cities/search', ['as' => 'api_tdriver_cities_search', 'uses' => 'ViajeController@searchCiudades']);
    Route::get('/cities/{cityId}/stores', ['as' => 'api_tdriver_cities_stores', 'uses' => 'ViajeController@getStoresByCity']);
    Route::get('/users/{userId}/travel', ['as' => 'api_tdriver_ver_paquetes', 'uses' => 'ViajeController@getPaquetes']);
    Route::post('/users/{userId}/viajes', ['as' => 'api_tdriver_reservar_paquetes', 'uses' => 'ViajeController@postViaje']);

    // VIAJES RUTA / HISTORIAL
    Route::get('/users/{userId}/travels', ['as' => 'api_tdriver_get_viajes', 'uses' => 'ViajeController@getViajes']);

    // LOCALIZACION
    Route::post('/users/{userId}/travels/{viajeId}/tracking', ['as' => 'api_tdriver_post_location', 'uses' => 'ViajeController@track']);

    // ALERTAS
    Route::get('/users/{userId}/alerts', ['as' => 'api_tdriver_get_alertas', 'uses' => 'AlertaController@getAlertas']);
    Route::post('/users/{userId}/alerts', ['as' => 'api_tdriver_post_alerta', 'uses' => 'AlertaController@postAlerta']);
    Route::put('/users/{userId}/alerts/{alertaId}', ['as' => 'api_tdriver_put_alerta', 'uses' => 'AlertaController@putAlerta']);
    Route::delete('/users/{userId}/alerts/{alertaId}', ['as' => 'api_tdriver_delete_alerta', 'uses' => 'AlertaController@deleteAlerta']);

    // MENSAJES
    Route::get('/users/{userId}/messages', ['as' => 'api_tdriver_get_mensajes', 'uses' => 'MensajeController@getMensajes']);
    Route::put('/users/{userId}/messages', ['as' => 'api_tdriver_read_mensajes', 'uses' => 'MensajeController@readMensajes']);

    // CUENTA
    Route::get('/users/{userId}', ['as' => 'api_tdriver_get_cuenta', 'uses' => 'CuentaController@getCuenta']);
    Route::get('/users/{userId}/income', ['as' => 'api_tdriver_get_ingresos_cuenta', 'uses' => 'CuentaController@getIngresos']);
    Route::get('/users/{userId}/income/{pagoId}', ['as' => 'api_tdriver_get_factura_cuenta', 'uses' => 'CuentaController@getFactura']);
    Route::get('/users/{userId}/billing', ['as' => 'api_tdriver_get_datos_facturacion_cuenta', 'uses' => 'CuentaController@getDatosFacturacion']);
    Route::put('/users/{userId}/billing', ['as' => 'api_tdriver_put_datos_facturacion_cuenta', 'uses' => 'CuentaController@putDatosFacturacion']);
    Route::put('/users/{userId}/billing/receipt', ['as' => 'api_tdriver_put_recibo_cuenta', 'uses' => 'CuentaController@putRecibo']);
    Route::get('/users/{userId}/charge', ['as' => 'api_tdriver_get_datos_bancarios_cuenta', 'uses' => 'CuentaController@getDatosCobro']);
    Route::put('/users/{userId}/charge', ['as' => 'api_tdriver_put_datos_bancarios_cuenta', 'uses' => 'CuentaController@putDatosCobro']);
    Route::put('/users/{userId}/password', ['as' => 'api_tdriver_put_password_cuenta', 'uses' => 'CuentaController@putPassword']);

    // PERFIL
    Route::get('/users/{userId}/data', ['as' => 'api_tdriver_get_datos_perfil', 'uses' => 'PerfilController@getDatos']);
    Route::put('/users/{userId}/data', ['as' => 'api_tdriver_put_datos_perfil', 'uses' => 'PerfilController@putDatos']);
    Route::post('/users/{userId}/image', ['as' => 'api_tdriver_post_imagen_perfil', 'uses' => 'PerfilController@postImagen']);
    Route::get('/users/{userId}/certificates', ['as' => 'api_tdriver_get_certificaciones_perfil', 'uses' => 'PerfilController@getCertificaciones']);
    Route::post('/users/{userId}/certificates', ['as' => 'api_tdriver_post_certificaciones_perfil', 'uses' => 'PerfilController@postCertificaciones']);
    Route::put('/users/{userId}/certificates', ['as' => 'api_tdriver_check_mobile_code_perfil', 'uses' => 'PerfilController@checkMobilePin']);
    Route::get('/users/{userId}/vehicle', ['as' => 'api_tdriver_get_vehiculo_perfil', 'uses' => 'PerfilController@getVehiculo']);
    Route::put('/users/{userId}/vehicle', ['as' => 'api_tdriver_put_vehiculo_perfil', 'uses' => 'PerfilController@putVehiculo']);
    Route::put('/users/{userId}/vehicle/transport-card', ['as' => 'api_tdriver_put_tarjeta_transporte_vehiculo_perfil', 'uses' => 'PerfilController@putTarjetaTransporte']);
    Route::put('/users/{userId}/vehicle/goods-insurance', ['as' => 'api_tdriver_put_seguro_mercancias_vehiculo_perfil', 'uses' => 'PerfilController@putSeguroMercancias']);

});