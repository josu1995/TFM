<?php

/*
|--------------------------------------------------------------------------
| API STORES
|--------------------------------------------------------------------------
*/

// Versión 1
Route::group(['prefix' => 'v1'], function () {

    // Auth
    Route::get('/version', ['as' => 'api_version', 'uses' => 'LoginController@getVersion']);
    Route::post('/login', ['as' => 'api_login', 'uses' => 'LoginController@login']);
    Route::post('/registro/validar', ['as' => 'api_validar_registro', 'uses' => 'LoginController@validarRegistro']);
    Route::post('/login/validar', ['as' => 'api_validar_login', 'uses' => 'LoginController@validarLogin']);


    // Usuario
    Route::get('/usuario', ['as' => 'api_usuario_actual', 'uses' => 'UsuarioController@getUsuario']);

    // Punto de entrega/recogida
    Route::get('/punto', ['as' => 'api_puntos', 'uses' => 'PuntoController@getIndex']);
    Route::get('/punto/{id}', ['as' => 'api_punto', 'uses' => 'PuntoController@getPunto']);
    Route::put('/punto/{id}', ['as' => 'api_punto_actualizar', 'uses' => 'PuntoController@putPunto']);
    // Modo almacén lleno
    Route::get('/punto/{id}/almacen', ['as' => 'api_punto_almacen', 'uses' => 'PuntoController@getAlmacen']);
    Route::put('/punto/{id}/almacen', ['as' => 'api_punto_almacen_modificar', 'uses' => 'PuntoController@putAlmacen']);
    // Comisiones: pendientes y pagadas
    Route::get('/punto/{id}/comisiones', ['as' => 'api_punto_comisiones', 'uses' => 'PuntoController@getComisiones']);
    // Historial de envíos & envíos actualmente en el puntp
    Route::get('/punto/{id}/historial', ['as' => 'api_punto_historial', 'uses' => 'PuntoController@getHistorial']);
    Route::get('/punto/{id}/envios', ['as' => 'api_punto_envios', 'uses' => 'PuntoController@getEnvios']);
    // Horarios
    Route::get('/punto/{id}/horario', ['as' => 'api_punto_horarios', 'uses' => 'PuntoController@getHorarios']);
    Route::post('/punto/{id}/horario', ['as' => 'api_punto_horarios_nuevo', 'uses' => 'PuntoController@postHorario']);
    Route::delete('/punto/{id}/horario', ['as' => 'api_punto_horarios_borrar', 'uses' => 'PuntoController@deleteHorario']);
    // Stock
    Route::get('/punto/{id}/stock', ['as' => 'api_punto_stock', 'uses' => 'PuntoController@getStock']);
    Route::put('/punto/{id}/stock', ['as' => 'api_punto_put_stock', 'uses' => 'PuntoController@putStock']);
    // Cierre
    Route::post('/punto/{id}/cierre', ['as' => 'api_punto_post_fecha_cierre', 'uses' => 'PuntoController@postFechaCierre']);

    // Localidades
    Route::get('/localidad', ['as' => 'api_localidades', 'uses' => 'LocalidadController@getIndex']);
    Route::get('/localidad/search', ['as' => 'api_localidad_search', 'uses' => 'LocalidadController@searchLocalidad']);
    Route::get('/localidad/{id}', ['as' => 'api_localidad', 'uses' => 'LocalidadController@getLocalidad']);

    // Envios
    Route::get('/envio', ['as' => 'api_envio', 'uses' => 'EnvioController@getEnvio']);
    Route::post('/envio/{codigo}/localizador', ['as' => 'api_envio_localizador_nuevo', 'uses' => 'EnvioController@postLocalizador']);
    Route::put('/envio/{codigo}/estado', ['as' => 'api_envio_cambio_estado', 'uses' => 'EnvioController@putEstado']);
    Route::post('/envio/{codigo}/destinatario/pin', ['as' => 'api_envio_entrega_pin', 'uses' => 'EnvioController@checkPIN']);
    Route::post('/envio/{codigo}/destinatario/dni', ['as' => 'api_envio_cambio_dni', 'uses' => 'EnvioController@putDNI']);
    Route::put('/envio/{codigo}/destinatario/dni', ['as' => 'api_envio_cambio_dni', 'uses' => 'EnvioController@putDNI']);

    // Pedido
    Route::get('/pedido/{identificador}/punto/{id}', ['as' => 'api_pedido', 'uses' => 'PedidoController@getPedido']);

    // Viaje (transporte)
    Route::get('/viaje/{codigo}/punto/{id}', ['as' => 'api_viaje', 'uses' => 'ViajeController@getViaje']);

    // Coberturas
    Route::get('/cobertura', ['as' => 'api_coberturas', 'uses' => 'CoberturaController@getCoberturas']);
    Route::get('/cobertura/{id}', ['as' => 'api_cobertura', 'uses' => 'CoberturaController@getCobertura']);

    // Métodos de cobro/pago
    Route::get('/metodo', ['as' => 'api_metodos', 'uses' => 'MetodoController@getMetodos']);
    Route::get('/metodo/{id}', ['as' => 'api_metodo', 'uses' => 'MetodoController@getMetodo']);

    // Precios
    Route::get('/precio', ['as' => 'api_precios', 'uses' => 'PrecioController@getPrecios']);
    Route::get('/precio/peso/{peso?}', ['as' => 'api_precio', 'uses' => 'PrecioController@getPrecioPorPeso']);

    // Recogida de errores
    Route::post('/errors', ['as' => 'api_errors', 'uses' => 'ErrorController@postError']);

});