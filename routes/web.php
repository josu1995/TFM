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


    // Login, registro, recordar credenciales
    Route::get('/login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('/login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@login']);
    Route::get('/logout', ['as' => 'auth.logout', 'uses' => 'Auth\LoginController@logout']);

    Route::get('/registro', ['as' => 'business_register', 'uses' => '\App\Http\Controllers\Business\BusinessController@getRegistro']);

    Route::post('/registro', ['as' => 'auth.register', 'uses' => 'Auth\RegisterController@register']);
    

    Route::get('/password/reset', ['as' => 'auth.password.reset.request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::get('/password/reset/{token?}', ['as' => 'auth.password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
    Route::post('/password/reset', ['as' => 'auth.password.reset', 'uses' => 'Auth\ResetPasswordController@reset']);
    Route::post('/password/email', ['as' => 'auth.password.email',  'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);


});