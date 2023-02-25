<?php

// Faccebook leads

Route::get('/facebook/lead', ['as' => 'facebook_leads', 'uses' => 'Web\Auth\RegisterController@getFacebookLead']);
Route::post('/facebook/lead', ['as' => 'facebook_leads_post', 'uses' => 'Web\Auth\RegisterController@postFacebookLead']);

// Ruta de vinculacion de cuenta
Route::group(['prefix' => 'vinculacion', 'namespace' => 'Web', 'domain' => env('APP_DOMAIN')], function() {
    // Formulario de vinculacion
    Route::get('/vinculacion', ['as' => 'get_vinculacion_cuenta', 'uses' => 'Auth\VinculacionController@getVinculacion']);
    Route::post('/vinculacion', ['as' => 'post_vinculacion_cuenta', 'uses' => 'Auth\VinculacionController@postVinculacion']);
});

// Ruta de encuesta a destinatario
Route::group(['namespace' => 'Encuesta', 'prefix' => 'encuesta', 'domain' => env('APP_DOMAIN')], function() {
    // Formulario de encuesta
    Route::get('/encuesta', ['as' => 'encuesta.index', 'uses' => 'EncuestaController@getIndex']);
    Route::post('/encuesta', ['as' => 'encuesta.creacion', 'uses' => 'EncuestaController@postEncuesta']);

});

// Ruta de opinion del emisor
Route::group(['namespace' => 'Encuesta', 'prefix' => 'opinion', 'domain' => env('APP_DOMAIN')], function() {
    // Formulario de encuesta
    Route::get('/opinion', ['as' => 'opinion.index', 'uses' => 'OpinionController@getIndex']);
    Route::post('/opinion', ['as' => 'opinion.enviar', 'uses' => 'OpinionController@postOpinion']);

});

// Landing stores
/*
Route::group(['domain' => env('APP_DOMAIN'), 'namespace' => 'Stores'], function () {
    // Portada de stores
    Route::get('/stores', ['as' => 'stores_portada', 'uses' => 'StoresController@index']);
    Route::post('/stores', ['as' => 'stores_crear_store', 'uses' => 'StoresController@postStore']);
});
*/

// Dominio principal
Route::group(['namespace' => 'Web','domain' => env('APP_DOMAIN')], function() {

    // Páginas estáticas (portada, ayuda, etc.)
    //Route::get('/index', ['as' => 'business_landing_index', 'uses' => 'WebController@index']);

    // Login, registro, recordar credenciales
    Route::get('/login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('/login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@login']);
    Route::get('/logout', ['as' => 'auth.logout', 'uses' => 'Auth\LoginController@logout']);

    Route::get('/registro', ['as' => 'business_register', 'uses' => '\App\Http\Controllers\Business\BusinessController@getRegistro']);
    // Route::get('/registro', ['as' => 'auth.register', 'uses' => 'Auth\RegisterController@showRegistrationForm']);
    Route::post('/registro', ['as' => 'auth.register', 'uses' => 'Auth\RegisterController@register']);
    

    Route::get('/password/reset', ['as' => 'auth.password.reset.request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::get('/password/reset/{token?}', ['as' => 'auth.password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
    Route::post('/password/reset', ['as' => 'auth.password.reset', 'uses' => 'Auth\ResetPasswordController@reset']);
    Route::post('/password/email', ['as' => 'auth.password.email',  'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);

    // Login oauth facebook
    Route::get('/facebook', ['as' => 'login_facebook', function(){
        return SocialAuth::authorize('facebook');
    }]);

    Route::get('/facebook/permissions', ['as' => 'login_facebook_permissions', function(){
        if(Auth::user() && is_null(Auth::user()->email)) {
            $auth = SocialAuth::authorize('facebook');
            $auth->setTargetUrl(str_replace(env('FACEBOOK_REDIRECT'), env('FACEBOOK_PERMISSIONS_REDIRECT'), $auth->getTargetUrl()) . '&auth_type=rerequest');
            return $auth;
        }
        return redirect('/');
    }]);

    Route::get('/facebook/permissions/redirect', ['as' => 'login_facebook_permissions_redirect', function(){

        $user = Auth::user();

        if(!is_null($user->email)) {
            return redirect(route('inicio'));
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.12/me?access_token=' . $user->usuarioOauth->access_token . '&fields=email,birthday');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);

        $info = json_decode($output);

        if(isset($info->email)) {
            $user->email = $info->email;
        }

        if(isset($info->birthday)) {
            $user->configuracion->fecha_nacimiento = date('Y-m-d', strtotime($info->birthday));
        }

        $user->save();

        return redirect(route('inicio'));

    }]);

    Route::get('/facebook/redireccion', ['as' => 'proceso_login_facebook', 'uses' => 'Auth\LoginController@getFacebook']);

    Route::group(['prefix' => 'tracking'], function () {
        Route::get('/{localizador}', ['as' => 'tracking_index', 'uses' => 'TrackingController@index']);
    });

//    Route::group(['prefix' => 'busqueda-stores'], function () {
//        Route::get('/', ['as' => 'busqueda_stores_index', 'uses' => 'StoresSearchController@index']);
//    });

    // Home de usuario
    // Route::group(['prefix' => 'inicio'], function () {
    //     Route::get('/', ['as' => 'inicio', 'uses' => 'HomeController@index']);
    //     Route::get('/envios', ['as' => 'envios', 'uses' => 'HomeController@getEnvios']);
    //     Route::get('/envios/pendientes',['as' => 'envios_pendientes', 'uses' => 'HomeController@getEnviosPendientes']);
    //     Route::get('/mensajes', ['as' => 'mensajes', 'uses' => 'HomeController@getMensajes']);
    //     Route::get('/permisos', ['as' => 'permisos', 'uses' => 'HomeController@getPermisos']);

    //     Route::group(['prefix' => '/perfil'], function() {
    //         Route::get('/', ['as' => 'perfil_usuario', 'uses' => 'HomeController@getPerfil']);
    //         Route::get('/metodo-pago', ['as' => 'perfil_metodo_pago', 'uses' => 'HomeController@getMetodoPago']);
    //         Route::delete('/metodo-pago/{id}', ['as' => 'perfil_metodo_pago_delete', 'uses' => 'HomeController@deleteMetodoPago']);
    //         Route::get('/metodo-pago/verificar', ['as' => 'perfil_verificar_tarjeta', 'uses' => 'HomeController@verificarTarjeta']);
    //         Route::get('/password', ['as' => 'perfil_password', 'uses' => 'HomeController@getPassword']);
    //         Route::get('/imagen', ['as' => 'perfil_imagen', 'uses' => 'HomeController@getImagen']);
    //         Route::get('/direccion_postal', ['as' => 'direccion_postal', 'uses' => 'HomeController@getDireccionPostal']);
    //         Route::get('/certificados', ['as' => 'perfil_certificados', 'uses' => 'HomeController@getCertificados']);
    //         Route::put('/actualizacion', ['as' => 'perfil_actualizacion', 'uses' => 'HomeController@putPerfil']);
    //         Route::put('/direccion-actualizacion', ['as' => 'direccion_actualizacion', 'uses' => 'HomeController@putDireccion']);

    //     });
    //     Route::group(['prefix' => '/cuenta'], function() {
    //         Route::get('/', ['as' => 'cuenta_usuario', 'uses' => 'HomeController@getCuenta']);
    //         Route::get('/metodo-pago', ['as' => 'cuenta_metodo_pago', 'uses' => 'HomeController@getMetodoPago']);
    //         Route::delete('/metodo-pago/{id}', ['as' => 'cuenta_metodo_pago_delete', 'uses' => 'HomeController@deleteMetodoPago']);
    //         Route::get('/metodo-pago/verificar', ['as' => 'cuenta_verificar_tarjeta', 'uses' => 'HomeController@verificarTarjeta']);
    //         Route::get('/password', ['as' => 'cuenta_password', 'uses' => 'HomeController@getPassword']);
    //         Route::get('/pagos-efectuados', ['as' => 'resumen_pedidos', 'uses' => 'HomeController@getPagosEfectuados']);
    //     });

    //     // Update de perfil
    //     Route::put('/perfil', ['as' => 'actualizacion_perfil', 'uses' => 'HomeController@putPerfil']);
    //     // Imagen de usuario
    //     Route::post('/imagen', ['as' => 'imagen_usuario', 'uses' => 'HomeController@postImagen']);
    // });

    // Validación de email y móvil
    Route::group(['prefix' => 'validacion'], function () {
        Route::get('/email/{codigo}', ['as' => 'validacion_email', 'uses' => 'HomeController@getValidacionEmail']);
        Route::post('/email', ['as' => 'enviar_validacion_mail', 'uses' => 'HomeController@postValidacionEmail']);
        Route::get('/movil', ['as' => 'enviar_validacion_movil', 'uses' => 'HomeController@getValidacionMovil']);
        Route::post('/movil', ['as' => 'validacion_movil', 'uses' => 'HomeController@postValidacionMovil']);
    });

    // CRUD de envíos
    
    Route::group(['prefix' => 'envio'], function () {
        // Proceso completo
        Route::get('/nuevo', ['as' => 'formulario_envio', 'uses' => 'EnvioController@index']);
        Route::post('/nuevo', ['as' => 'crear_envio', 'uses' => 'EnvioController@postEnvio']);

        Route::get('/pago/{codigo}', ['as' => 'resumen_pago', 'uses' => 'EnvioController@getPago']);
        Route::post('/pago/{codigo}', ['as' => 'crear_pago', 'uses' => 'EnvioController@postPago']);

        Route::get('/edicion/{codigo}',  ['as' => 'resumen_envio', 'uses' => 'EnvioController@getEnvio']);
        Route::put('/edicion/{codigo}',  ['as' => 'editar_envio', 'uses' => 'EnvioController@putEnvio']);

        Route::delete('/{codigo}',  ['as' => 'borrar_envio', 'uses' => 'EnvioController@deleteEnvio']);

        // Pago múltiple
        Route::get('/pagar-todos', ['as' => 'resumen_pagos_todos', 'uses' => 'EnvioController@actualizarPagosPendientes']);
        Route::get('/pago',  'EnvioController@getPagosPendientes')->name('resumen_pagos');
        Route::post('/pago',  ['as' => 'crear_pagos', 'uses' => 'EnvioController@postPagosPendientes']);

        // Pagar mas tarde
        Route::post('/retrasar',  ['as' => 'retrasar_pago', 'uses' => 'EnvioController@postRetrasarPago']);

        // Retorno de pago por paypal
        Route::get('/pago/paypal/success', ['as' => 'pago_success_paypal', 'uses' => 'EnvioController@getPagoSuccessPaypal']);

        Route::get('/{other}', ['as' => 'envio_other', function() {
            abort(404);
        }]);
    });
    

    // Rutas
    Route::get('/origen/{idOrigen}/destino/{idDestino}', ['as' => 'rutas_alertas', 'uses' => 'LocalidadController@getRutas']);

    // Lightbox de punto creación envío & viaje
    Route::get('/punto/{id}', ['as' => 'punto_envio', 'uses' => 'PuntoController@showEnvio']);
    Route::get('/punto/{idDestino}/desde/{idOrigen}', ['as' => 'punto_viaje', 'uses' => 'PuntoController@showViaje']);
    Route::get('/punto/{idDestino}/desde/{idOrigen}/lista', ['as' => 'punto_viaje_paquetes', 'uses' => 'PuntoController@getLista']);
    Route::get('/punto/{id}/viaje', ['as' => 'punto_viaje_inicio', 'uses' => 'PuntoController@showViajeInicio']);

    // Páginas de retorno de pago de tarjeta (asíncrona)
    Route::post('/tpv/cobro/success', ['as' => 'admin_success_cobro', 'uses' => 'TpvController@postSuccessCobro']);
    Route::post('/metodo-pago/verificar/success', ['as' => 'verificar_tarjeta_success', 'uses' => 'HomeController@postSuccessVerificarTarjeta']);
    Route::post('/tpv/success', ['as' => 'pago_success_tarjeta', 'uses' => 'EnvioController@postPagoSuccessTarjeta']);
    Route::post('/tpv/viaje/success', ['as' => 'viaje_success_tarjeta', 'uses' => 'TpvController@postNotificacion']);
    Route::post('/tpv/business/envios/success', ['as' => 'pagar_envios_business_success', 'uses' => 'TpvController@pagarEnviosBusinessSuccess']);
    Route::post('/tpv/business/devolucion/success', ['as' => 'pagar_devolucion_business_success', 'uses' => 'TpvController@pagarDevolucionBusinessSuccess']);

    // Ruta de blog
    Route::group(['prefix' => 'blog'], function() {
        Route::get('/', ['as' => 'blog_get_index', 'uses' => 'BlogController@getIndex']);
        Route::get('/buscador', ['as' => 'blog_buscar', 'uses' => 'BlogController@search']);
        Route::get('/cargar', ['as' => 'blog_cargar', 'uses' => 'BlogController@loadMore']);
        Route::get('/{post}', ['as' => 'blog_post', 'uses' => 'BlogController@getPost']);
    });

    // Rutas para códigos de descuento
    Route::group(['prefix' => 'codigos'], function() {
        Route::post('/validate', ['as' => 'codigo_validar', 'uses' => 'CodigoController@validarCodigo']);
        Route::delete('/', ['as' => 'codigo_eliminar', 'uses' => 'CodigoController@eliminarCodigo']);
    });

    // Páginas estáticas por categoría. Nota: Estas rutas tienen que ir al final de todo
    Route::group(['prefix' => ''], function () {
        Route::get('/buscador/pagina', ['as' => 'buscador_paginas', 'uses' => 'BuscadorController@getPagina']);
        // Route::group( [ 'middleware' => 'auth' ], function( ) {
		Route::group( [ 'middleware' => 'auth.business' ], function( ) {
            Route::get('/ayuda', ['as' => 'muestra_inicio_ayuda', 'uses' => 'WebController@getHelpPage']);
            Route::get('/ayuda/{slug2}/{slug3?}', ['as' => 'muestra_pagina_ayuda', 'uses' => 'WebController@getPaginaAyuda']);
        } );
        Route::get('/{slug}', ['as' => 'muestra_categoria', 'uses' => 'WebController@getCategoria']);
        Route::get('/informacion/{slug2}/{slug3?}', ['as' => 'muestra_pagina_informacion', 'uses' => 'WebController@getPaginaInformacion']);
    });

});