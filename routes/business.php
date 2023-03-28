<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Business\Inventario\ExistenciasController;
use App\Http\Controllers\Business\Inventario\MovimientosController;
use App\Http\Controllers\Business\Configuracion\PuntoRecogidaController;
use App\Http\Controllers\Business\Configuracion\AlmacenRecogidaController;

Route::get('/', ['as' => 'business_landing_index', 'uses' => 'BusinessController@index']);
// Route::group(['prefix' => '/business'], function() {
Route::post('/businessLogin', ['as' => 'business_login', 'uses' => 'LoginController@login']);
Route::post('/login/validar', ['as' => 'business_validar_login', 'uses' => 'LoginController@validarLogin']);
//Route::get('/registro', ['as' => 'business_register', 'uses' => 'BusinessController@getRegistro']);
Route::post('/registroBusiness', ['as' => 'business_post_register', 'uses' => 'BusinessController@postRegistro']);
Route::get('/registro/informacion', ['as' => 'business_register_informacion', 'uses' => 'BusinessController@getRegistroInformacion']);
Route::post('/registro/informacion', ['as' => 'business_post_register_informacion', 'uses' => 'BusinessController@postRegistroInformacion']);
Route::get('/businessLogout', ['as' => 'business_logout', 'uses' => 'LoginController@logout']);

Route::group(['prefix' => '/home'], function () {
    Route::get('/badges', ['as' => 'business_home_badges', 'uses' => 'HomeController@getBadges']);
});

Route::group(['prefix' => '/nuevo-envio'], function () {
    Route::get('/crear', ['as' => 'business_envios_crear', 'uses' => 'NuevoEnvioController@getCrear']);
    Route::post('/crear', ['as' => 'business_post_envio', 'uses' => 'NuevoEnvioController@postEnvio']);
    Route::get('/importar', ['as' => 'business_envios_importar', 'uses' => 'NuevoEnvioController@getImportar']);
    Route::get('/importar-plantilla-excel', ['as' => 'business_configuracion_envios_importar_plantilla_excel', 'uses' => 'NuevoEnvioController@descargarPlantillaImportarExcel']);
    Route::post('/importar', ['as' => 'business_post_envios_importar', 'uses' => 'NuevoEnvioController@postImportar']);
    Route::post('/importar/omit', ['as' => 'business_post_envios_importar_omitir', 'uses' => 'NuevoEnvioController@omitImportErrors']);
});

Route::group(['prefix' => '/admin'], function () {
    Route::get('/palabras', ['as' => 'business_envios_pendientes_pago', 'uses' => 'AdminController@getPalabras']);
    Route::get('/buscar',['as' => 'admin_buscar', 'uses' => 'AdminController@buscar']);
    Route::post('/crearPalabra',['as' =>'admin_crear_palabra','uses' => 'AdminController@crearPalabra']);
    Route::post('/editarPalabra/{id}',['as' => 'admin_editar_palabra','uses' => 'AdminController@editarPalabra']);
    Route::post('eliminarPalabra',['as' => 'admin_eliminar_palabra','uses' => 'AdminController@eliminarPalabra']);
    Route::get('/usuarios',['as' => 'admin_usuarios','uses' => 'AdminController@getUsuarios']);
    Route::post('/editarConfiguracion/{id}',['as' => 'admin_editar_configuracion','uses' => 'AdminController@editConfiguracion']);
    Route::post('eliminarConfiguracion',['as' => 'admin_eliminar_configuracion','uses' => 'AdminController@deleteConfiguracion']);
    Route::get('/buscarUsuario',['as' => 'admin_buscar_usuario', 'uses' => 'AdminController@buscarUsuario']);
    Route::get('/descargarPlantilla',['as' => 'admin_plantilla', 'uses' => 'AdminController@descargarPlantilla']);
    Route::post('/importarExcel',['as' => 'admin_importar_excel', 'uses' => 'AdminController@importarExcel']);
    Route::get('/corregirRedaccion',['as' => 'admin_redaccion', 'uses' => 'AdminController@corregirRedaccion']);
    Route::get('/buscarRedaccion',['as' => 'admin_buscar_redaccion', 'uses' => 'AdminController@buscarRedaccion']);
    Route::get('/getRedaccion/{id}',['as' => 'admin_get_redaccion', 'uses' => 'AdminController@getRedaccion']);
    Route::post('/saveRedaccion/{id}',['as' => 'admin_save_redaccion', 'uses' => 'AdminController@saveRedaccion']);

});

Route::group(['prefix' => '/usuario'],function(){
    Route::get('/perfilUsuario', ['as' => 'usuario_get_datos', 'uses' => 'HomeController@getDatosUsuario']);
    Route::post('/editarPerfil', ['as' => 'usuario_post_perfil', 'uses' => 'HomeController@postPerfil']);
    Route::post('/editarContraseña', ['as' => 'usuario_post_contraseña', 'uses' => 'HomeController@postContraseña']);
    Route::get('/estudios', ['as' => 'usuario_get_estudios', 'uses' => 'HomeController@getEstudios']);
    Route::post('/crearConfiguracion',['as' => 'usuario_new_configuracion','uses' => 'HomeController@crearNuevaConfiguracion']);
    Route::get('/jugar/{id}',['as' => 'usuario_jugar', 'uses' => 'HomeController@jugar']);
    Route::get('/comprobar',['as' => 'usuario_comprobar', 'uses' => 'HomeController@comprobar']);
    Route::get('/redaccion', ['as' => 'usuario_get_redaccion', 'uses' => 'HomeController@getRedaccion']);
    Route::get('/nuevaRedaccion', ['as' => 'usuario_get_nueva_redaccion', 'uses' => 'HomeController@getNuevaRedaccion']);
    Route::post('/crearRedaccion',['as' => 'usuario_new_redaccion','uses' => 'HomeController@crearNuevaRedaccion']);
    Route::get('/correccion/{id}', ['as' => 'usuario_get_correccion', 'uses' => 'HomeController@getCorreccion']);
    Route::get('lang/{lang}', 'LanguageController@swap')->name('lang.swap');
    Route::get('/atencion-cliente', ['as' => 'business_ayuda', 'uses' => 'HomeController@ayuda']);
    
   
   
});
