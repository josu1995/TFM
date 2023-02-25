<?php

/*
Route::get('/', ['as' => 'drivers_landing_index', 'uses' => 'DriverController@index']);
Route::post('/', ['as' => 'drivers_landing_register', 'uses' => 'DriverController@register']);
Route::get('/register', ['as' => 'drivers_getRegister', 'uses' => 'RegisterController@index']);
Route::post('/register', ['as' => 'drivers_register', 'uses' => 'RegisterController@registerProfesional']);
Route::post('/register/particular', ['as' => 'drivers_register_particular', 'uses' => 'RegisterController@register']);
Route::post('/login', ['as' => 'drivers_login', 'uses' => 'LoginController@login']);
Route::get('/logout', ['as' => 'drivers_logout', 'uses' => 'LoginController@logout']);

// Panel de control
Route::group(['prefix' => 'inicio'], function () {
    Route::get('/', ['as' => 'drivers_inicio', 'uses' => 'HomeController@index']);
    Route::get('/viajes', ['as' => 'drivers_viajes', 'uses' => 'HomeController@getViajes']);
    Route::get('/viajes/{id}/ver-paquetes', ['as' => 'drivers_viajes-ver-paquetes', 'uses' => 'HomeController@getEnviosViajes']);
    Route::delete('/viajes/{id}/cancelar', ['as' => 'drivers_cancelar_viaje', 'uses' => 'HomeController@cancelarViaje']);
    Route::get('/mensajes', ['as' => 'drivers_mensajes', 'uses' => 'HomeController@getMensajes']);
    Route::get('/alertas', ['as' => 'drivers_alertas', 'uses' => 'HomeController@getAlertas']);
    Route::delete('/alertas/{id}', ['as' => 'drivers_alerta_delete', 'uses' => 'HomeController@deleteAlerta']);
    Route::put('/alertas/{id}', ['as' => 'drivers_alerta_editar', 'uses' => 'HomeController@putAlerta']);
    Route::post('/alertas/puntual', ['as' => 'drivers_new_alerta_puntual', 'uses' => 'HomeController@postAlertaPuntual']);
    Route::post('/alertas/habitual', ['as' => 'drivers_new_alerta_habitual', 'uses' => 'HomeController@postAlertaHabitual']);

    Route::group(['prefix' => '/perfil'], function() {
        Route::get('/', ['as' => 'drivers_perfil_usuario', 'uses' => 'HomeController@getPerfil']);
        Route::get('/metodo-pago', ['as' => 'drivers_perfil_metodo_pago', 'uses' => 'HomeController@getMetodoPago']);
        Route::delete('/metodo-pago/{id}', ['as' => 'drivers_perfil_metodo_pago_delete', 'uses' => 'HomeController@deleteMetodoPago']);
        Route::get('/metodo-pago/verificar', ['as' => 'drivers_perfil_verificar_tarjeta', 'uses' => 'HomeController@verificarTarjeta']);
        Route::get('/imagen', ['as' => 'drivers_perfil_imagen', 'uses' => 'HomeController@getImagen']);
        Route::get('/direccion_postal', ['as' => 'drivers_direccion_postal', 'uses' => 'HomeController@getDireccionPostal']);
        Route::get('/certificados', ['as' => 'drivers_perfil_certificados', 'uses' => 'HomeController@getCertificados']);
        Route::put('/actualizacion', ['as' => 'drivers_perfil_actualizacion', 'uses' => 'HomeController@putPerfil']);
        Route::put('/direccion-actualizacion', ['as' => 'drivers_direccion_actualizacion', 'uses' => 'HomeController@putDireccion']);

    });
    Route::group(['prefix' => '/cuenta'], function() {
        Route::get('/', ['as' => 'drivers_cuenta_usuario', 'uses' => 'HomeController@getCuenta']);
        Route::get('/preferencias-cobro', ['as' => 'drivers_cuenta_preferencias_cobro', 'uses' => 'HomeController@getPreferenciasCobro']);
        Route::post('/preferencias-cobro', ['as' => 'drivers_cuenta_post_preferencia_cobro', 'uses' => 'HomeController@postPreferenciaCobro']);
        Route::post('/preferencias-cobro/{id}/defecto', ['as' => 'drivers_cuenta_preferencias_cobro_defecto', 'uses' => 'HomeController@selectPorDefecto']);
        Route::put('/preferencias-cobro/{id}', ['as' => 'drivers_cuenta_put_preferencia_cobro', 'uses' => 'HomeController@putPreferenciaCobro']);
        Route::delete('/preferencias-cobro/{id}', ['as' => 'drivers_cuenta_delete_preferencia_cobro', 'uses' => 'HomeController@deletePreferenciaCobro']);
        Route::get('/metodo-pago', ['as' => 'drivers_cuenta_metodo_pago', 'uses' => 'HomeController@getMetodoPago']);
        Route::delete('/metodo-pago/{id}', ['as' => 'drivers_cuenta_metodo_pago_delete', 'uses' => 'HomeController@deleteMetodoPago']);
        Route::get('/metodo-pago/verificar', ['as' => 'drivers_cuenta_verificar_tarjeta', 'uses' => 'HomeController@verificarTarjeta']);
        Route::get('/password', ['as' => 'drivers_cuenta_password', 'uses' => 'HomeController@getPassword']);
        Route::get('/saldo-recibido', ['as' => 'drivers_cuenta_saldo_recibido', 'uses' => 'HomeController@getSaldoRecibido']);
    });

    // Update de perfil
    Route::put('/perfil', ['as' => 'drivers_actualizacion_perfil', 'uses' => 'HomeController@putPerfil']);
    // Imagen de usuario
    Route::post('/imagen', ['as' => 'drivers_imagen_usuario', 'uses' => 'HomeController@postImagen']);

});

// Validación de email y móvil
Route::group(['prefix' => 'validacion'], function () {
    Route::get('/email/{codigo}', ['as' => 'drivers_validacion_email', 'uses' => 'HomeController@getValidacionEmail']);
    Route::post('/email', ['as' => 'drivers_enviar_validacion_mail', 'uses' => 'HomeController@postValidacionEmail']);
    Route::get('/movil', ['as' => 'drivers_enviar_validacion_movil', 'uses' => 'HomeController@getValidacionMovil']);
    Route::post('/movil', ['as' => 'drivers_validacion_movil', 'uses' => 'HomeController@postValidacionMovil']);
});

// Viaje
Route::group(['prefix' => 'viajar'], function () {
    // Selección/deselección de envíos
    Route::get('/', ['as' => 'drivers_buscar_viaje', 'uses' => 'ViajeController@index']);
    Route::post('/', ['as' => 'seleccionar_envio', 'uses' => 'ViajeController@postSeleccion']);
    Route::delete('/', ['as' => 'deseleccionar_envio', 'uses' => 'ViajeController@deleteSeleccion']);

    // Eliminar datos session
    Route::delete('/eliminar', ['as' => 'eliminar_session', 'uses' => 'ViajeController@deleteSession']);

    // Nuevo viaje
    Route::get('/seleccion/volver', ['as' => 'volver_seleccion_destino', 'uses' => 'ViajeController@volverSeleccionDestino']);
    Route::get('/seleccion', ['as' => 'get_seleccionar_destinos', 'uses' => 'ViajeController@getSeleccionarDestinos']);
    Route::post('/seleccion', ['as' => 'seleccionar_destinos', 'uses' => 'ViajeController@seleccionarDestinos']);
    Route::delete('/seleccion', ['as' => 'deseleccionar_envios', 'uses' => 'ViajeController@deseleccionarEnviosAll']);

    // Creación de viaje
    Route::post('/seleccionar', ['as' => 'seleccionar_paquetes_viaje', 'uses' => 'ViajeController@seleccionarPaquetes']);
    Route::get('/seleccionar/actualizar', ['as' => 'actualizar_seleccion_paquetes_viaje', 'uses' => 'ViajeController@actualizarSeleccionPaquetes']);
    Route::get('/resumen', ['as' => 'resumen_viaje', 'uses' => 'ViajeController@getResumen']);
    Route::get('/pagar', ['as' => 'resumen_pago_viaje', 'uses' => 'ViajeController@resumenPago']);
    Route::post('/pagar', ['as' => 'pagar', 'uses' => 'ViajeController@pagar']);

});

// Páginas estáticas por categoría. Nota: Estas rutas tienen que ir al final de todo
Route::group(['prefix' => ''], function () {
    Route::get('/buscador/pagina', ['as' => 'drivers_buscador_paginas', 'uses' => 'AyudaController@getPagina']);
    Route::get('/{slug}', ['as' => 'drivers_muestra_categoria', 'uses' => 'AyudaController@getCategoria']);
    Route::get('/ayuda/{slug2}/{slug3?}', ['as' => 'drivers_muestra_pagina_ayuda', 'uses' => 'AyudaController@getPaginaAyuda']);
    Route::get('/informacion/{slug2}/{slug3?}', ['as' => 'drivers_muestra_pagina_informacion', 'uses' => 'AyudaController@getPaginaInformacion']);
});
*/