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
    Route::post('eliminarPalabra',['as' => 'admin_eliminar_palabra','uses' => 'AdminController@eliminarPalabra']);
});

Route::group(['prefix' => '/devoluciones'], function () {
    Route::get('/pendientes', ['as' => 'business_devoluciones_pendientes', 'uses' => 'DevolucionesController@getPendientes']);
    Route::get('/pendientes/search', ['as' => 'business_devoluciones_pendientes_search', 'uses' => 'DevolucionesController@searchPendientes']);
    Route::get('/pendientes/export/pdf', ['as' => 'business_devoluciones_pendientes_export_pdf', 'uses' => 'DevolucionesController@exportarPdfPendientes']);
    Route::get('/pendientes/export/xls', ['as' => 'business_devoluciones_pendientes_export_xls', 'uses' => 'DevolucionesController@exportarXlsPendientes']);
    Route::get('/finalizadas', ['as' => 'business_devoluciones_finalizadas', 'uses' => 'DevolucionesController@getFinalizadas']);
    Route::get('/finalizadas/search', ['as' => 'business_devoluciones_finalizadas_search', 'uses' => 'DevolucionesController@searchFinalizadas']);
    Route::get('/finalizadas/export/pdf', ['as' => 'business_devoluciones_finalizadas_export_pdf', 'uses' => 'DevolucionesController@exportarPdfFinalizadas']);
    Route::get('/finalizadas/export/xls', ['as' => 'business_devoluciones_finalizadas_export_xls', 'uses' => 'DevolucionesController@exportarXlsFinalizadas']);
    Route::get('/etiqueta', ['as' => 'business_devoluciones_etiqueta', 'uses' => 'EtiquetaController@crearEtiquetasDevolucion']);
    Route::post('/etiqueta/enviar', ['as' => 'business_devoluciones_etiqueta_enviar', 'uses' => 'EtiquetaController@enviarEtiquetasDevolucion']);
    Route::get('/motivos/{id}', ['as' => 'business_devoluciones_motivo', 'uses' => 'DevolucionesController@getDetallesMotivo']);
});

Route::group(['prefix' => '/inventario', 'as' => 'business_inventario_'], function () {
    Route::group(['prefix' => '/productos'], function () {
        Route::get('/', ['as' => 'productos', 'uses' => 'Inventario\ProductoController@getProductos']);
        Route::get('/search', ['as' => 'productos_search', 'uses' => 'Inventario\ProductoController@searchProductos']);
        //Route::get('/search-data', ['as' => 'productos_search_data', 'uses' => 'Inventario\ProductoController@searchProductosData']);
        Route::post('/', ['as' => 'post_producto', 'uses' => 'Inventario\ProductoController@postProducto']);
        Route::put('/activar', ['as' => 'activar_productos', 'uses' => 'Inventario\ProductoController@activarProductos']);
        Route::put('/desactivar', ['as' => 'desactivar_productos', 'uses' => 'Inventario\ProductoController@desactivarProductos']);
        Route::put('/{id}', ['as' => 'put_producto', 'uses' => 'Inventario\ProductoController@putProducto']);
        Route::post('/importar-excel', ['as' => 'productos_importar_excel', 'uses' => 'Inventario\ProductoController@importProductosExcel']);
        Route::post('/importar-excel1', ['as' => 'productos_importar_existencias', 'uses' => 'Inventario\ExistenciasController@importarExcel']);
        Route::get('/importar-excel1/omit', ['as' => 'productos_importar_existencias_omitir', 'uses' => 'Inventario\ExistenciasController@omitirImportarExcel']);
        Route::post('/importar-excel/omit', ['as' => 'productos_importar_excel_omitir', 'uses' => 'Inventario\ProductoController@omitImportErrors']);
        Route::get('/plantilla-excel', ['as' => 'productos_descargar_plantilla_excel', 'uses' => 'Inventario\ProductoController@descargarPlantillaExcel']);
        Route::post('/seleccion', ['as' => 'productos_seleccionar', 'uses' => 'Inventario\ProductoController@postSeleccion']);
        Route::post('/filtrar', ['as' => 'productos_filtrar', 'uses' => 'Inventario\ProductoController@postFiltroActivos']);
        Route::post('/obtenerProductos', ['as' => 'obtener_productos', 'uses' => 'Inventario\ProductoController@obtenerProductos']);
        Route::get('/exportar-pdf', ['as' => 'productos_exportar_pdf', 'uses' => 'Inventario\ProductoController@exportarPdf']);
        Route::get('/exportar-pdf1', ['as' => 'productos_exportar_existencias_pdf', 'uses' => 'Inventario\ExistenciasController@exportarPdf']);
        Route::get('/exportar-excel', ['as' => 'productos_exportar_excel', 'uses' => 'Inventario\ProductoController@exportarExcel']);
        Route::get('/exportar-excel1', ['as' => 'productos_exportar_existencias', 'uses' => 'Inventario\ExistenciasController@descargarExistencias']);
        Route::get('/exportar-excel2', ['as' => 'productos_exportar_todo_excel', 'uses' => 'Inventario\ExistenciasController@exportarExcel']);
    
        Route::get('/showOrigen', ['as' => 'productos_show_origen', 'uses' => 'Inventario\ExistenciasController@showOrigen']);
    });
    Route::group(['prefix' => '/existencias', 'as' => 'existencias_'], function () {
        Route::get('/', [ExistenciasController::class, 'index'])->name('index');
        Route::get('/update', [ExistenciasController::class, 'update'])->name('update');
        Route::get('/filter', [ExistenciasController::class, 'filterStock'])->name('filter_stock');

    });

    Route::group(['prefix' => '/movimientos', 'as' => 'movimientos_'],function(){
        Route::get('/',[MovimientosController::class, 'index'])->name('index');
        Route::post('/cancelar',[MovimientosController::class, 'cancelar'])->name('cancelar');
        Route::get('/exportarPdf',[MovimientosController::class, 'exportarPdf'])->name('exportarPdf');
        Route::get('/exportarExcel',[MovimientosController::class, 'exportarExcel'])->name('exportarExcel');
        Route::get('/exportarEdicion/{id}',[MovimientosController::class,'exportarEdicion'])->name('exportarEdicion');
        Route::get('/filter', [MovimientosController::class, 'filterStock'])->name('filter_stock');
        Route::post('/omitir',[MovimientosController::class, 'omitir'])->name('omitir');
    });
});

Route::group(['prefix' => '/configuracion'], function () {

    Route::post('/changeMenu', ['as' => 'business_configuracion_changeMenu', 'uses' => 'Configuracion\TransportistaController@changeMenu']);

    Route::group(['prefix' => '/productos'], function () {
        Route::get('/', ['as' => 'business_configuracion_productos', 'uses' => 'Configuracion\ProductoController@getProductos']);
        Route::get('/search', ['as' => 'business_configuracion_productos_search', 'uses' => 'Configuracion\ProductoController@searchProductos']);
        Route::get('/search-data', ['as' => 'business_configuracion_productos_search_data', 'uses' => 'Configuracion\ProductoController@searchProductosData']);
        Route::post('/', ['as' => 'business_configuracion_post_producto', 'uses' => 'Configuracion\ProductoController@postProducto']);
        Route::put('/{id}', ['as' => 'business_configuracion_put_producto', 'uses' => 'Configuracion\ProductoController@putProducto']);

        Route::post('/importar-excel', ['as' => 'business_configuracion_productos_importar_excel', 'uses' => 'Configuracion\ProductoController@importProductosExcel']);
        Route::post('/importar-excel/omit', ['as' => 'business_configuracion_productos_importar_excel_omitir', 'uses' => 'Configuracion\ProductoController@omitImportErrors']);

        Route::get('/plantilla-excel', ['as' => 'business_configuracion_productos_descargar_plantilla_excel', 'uses' => 'Configuracion\ProductoController@descargarPlantillaExcel']);

        Route::post('/seleccion', ['as' => 'business_configuracion_productos_seleccionar', 'uses' => 'Configuracion\ProductoController@postSeleccion']);

        Route::get('/exportar-pdf', ['as' => 'business_configuracion_productos_exportar_pdf', 'uses' => 'Configuracion\ProductoController@exportarPdf']);

        Route::get('/exportar-excel', ['as' => 'business_configuracion_productos_exportar_excel', 'uses' => 'Configuracion\ProductoController@exportarExcel']);

        Route::delete('/', ['as' => 'business_configuracion_eliminar_productos', 'uses' => 'Configuracion\ProductoController@eliminarProductos']);
    });

    Route::group(['prefix' => '/paquetes'], function () {
        Route::get('/', ['as' => 'business_configuracion_paquetes', 'uses' => 'Configuracion\PaqueteController@getPaquetes']);
        Route::get('/search', ['as' => 'business_configuracion_paquetes_search', 'uses' => 'Configuracion\PaqueteController@searchPaquetes']);
        Route::post('/', ['as' => 'business_configuracion_post_paquete', 'uses' => 'Configuracion\PaqueteController@postPaquete']);
        Route::put('/{id}', ['as' => 'business_configuracion_put_paquete', 'uses' => 'Configuracion\PaqueteController@putPaquete']);

        Route::post('/importar-excel', ['as' => 'business_configuracion_paquetes_importar_excel', 'uses' => 'Configuracion\PaqueteController@importPaquetesExcel']);
        Route::post('/importar-excel/omit', ['as' => 'business_configuracion_paquetes_importar_excel_omitir', 'uses' => 'Configuracion\PaqueteController@omitPaquetesImportErrors']);

        Route::get('/plantilla-excel', ['as' => 'business_configuracion_paquetes_descargar_plantilla_excel', 'uses' => 'Configuracion\PaqueteController@descargarPlantillaExcelPaquetes']);

        Route::post('/seleccion', ['as' => 'business_configuracion_paquetes_seleccionar', 'uses' => 'Configuracion\PaqueteController@postPaquetesSeleccion']);

        Route::get('/exportar-pdf', ['as' => 'business_configuracion_paquetes_exportar_pdf', 'uses' => 'Configuracion\PaqueteController@exportarPdfPaquetes']);

        Route::get('/exportar-excel', ['as' => 'business_configuracion_paquetes_exportar_excel', 'uses' => 'Configuracion\PaqueteController@exportarExcelPaquetes']);

        Route::delete('/', ['as' => 'business_configuracion_eliminar_paquetes', 'uses' => 'Configuracion\PaqueteController@eliminarPaquetes']);
    });

    Route::group(['prefix' => '/transportistas'], function(){
        Route::get('/',  ['as' => 'business_configuracion_transportistas', 'uses' => 'Configuracion\TransportistaController@getTransportistas']);
        Route::post('{transportista}/deactivate',['as'=>'business_configuracion_deactivate_transportista','uses' => 'Configuracion\TransportistaController@deactivateTransportista' ]);
        Route::post('{transportista}/activate',['as'=>'business_configuracion_activate_transportista','uses' => 'Configuracion\TransportistaController@activateTransportista' ]);
        Route::post('{transportista}/configuracion',['as'=>'business_configuracion_transportista','uses' => 'Configuracion\TransportistaController@configuracionTransportista' ]);
        Route::get('{tipo}/filtro',['as'=>'business_filtro_transportista','uses' => 'Configuracion\TransportistaController@filtroTransportista' ]);
        Route::get('{transportista}/suprimirContrato',['as' => 'business_suprimir_contrato_transportista','uses' => 'Configuracion\TransportistaController@suprimirContrato']);
    });

    Route::group(['prefix' => '/checkout'], function(){
        Route::get('/{ordenar?}',  ['as' => 'business_configuracion_checkout', 'uses' => 'Configuracion\CheckOutController@getCheckOut']);
        Route::post('/editar/{id}',['as' => 'business_configuracion_editar_checkout', 'uses' => 'Configuracion\CheckOutController@editCheckOut']);
        Route::post('/editarPrecios',['as' => 'business_configuracion_editar_checkoutPrecios', 'uses' => 'Configuracion\CheckOutController@editCheckOutPrecios']);
        Route::get('/simular/{ordenar?}',['as' => 'business_configuracion_checkout_simular', 'uses' => 'Configuracion\CheckOutController@simular']);
        Route::post('/simularPrecio',['as' => 'business_configuracion_checkout_simular_precio', 'uses' => 'Configuracion\CheckOutController@simularPrecio']);
        Route::post('/simularPrediccion',['as' => 'business_configuracion_checkout_simular_prediccion', 'uses' => 'Configuracion\CheckOutController@simularPrediccion']);
        Route::post('/ordenar',['as' => 'business_configuracion_checkout_ordenar', 'uses' => 'Configuracion\CheckOutController@ordenar']);
        Route::post('/ordenarCards',['as' => 'business_configuracion_checkout_ordenar_cards', 'uses' => 'Configuracion\CheckOutController@ordenarCards']);
        Route::get('/obtenerPuntos/mapa',['as' => 'business_configuracion_checkout_obtenerPuntosMapa', 'uses' => 'Configuracion\CheckOutController@obtenerPuntosMapa']);
        Route::post('/change',['as' => 'business_configuracion_checkout_change', 'uses' => 'Configuracion\CheckOutController@change']);
        Route::post('/saveReglasCheckOut',['as' => 'business_configuracion_saveReglasCheckOut', 'uses' => 'Configuracion\CheckOutController@saveReglasCheckOut']);
        Route::post('/obtenerRegla',['as' => 'business_configuracion_checkout_obtenerRegla', 'uses' => 'Configuracion\CheckOutController@obtenerRegla']);
        Route::post('/obtenerAccion',['as' => 'business_configuracion_checkout_obtenerAccion', 'uses' => 'Configuracion\CheckOutController@obtenerAccion']);
        Route::post('/editReglasCheckOut',['as' => 'business_configuracion_editReglasCheckOut', 'uses' => 'Configuracion\CheckOutController@editReglasCheckOut']);
       
    });

    Route::group(['prefix' => '/reglas'],function(){
        Route::get('/buscar',['as' => 'business_configuracion_checkout_buscarReglas', 'uses' => 'Configuracion\CheckOutController@buscarReglas']);
        Route::post('/eliminarReglas',['as' => 'business_configuracion_checkout_eliminarReglas', 'uses' => 'Configuracion\CheckOutController@eliminarReglas']);
    });

   

    Route::group(['prefix' => '/punto-recogida', 'as' => 'business_configuracion_'], function () {
        Route::get('/', [PuntoRecogidaController::class, 'getPuntoRecogida'])->name('get_puntos_recogida');
        Route::post('/', [PuntoRecogidaController::class, 'postRecogida'])->name('post_recogida');
        Route::post('/selection', [PuntoRecogidaController::class, 'selectRecogida'])->name('select_recogida');
    });

    Route::group(['prefix' => '/almacenes-recogida', 'as' => 'business_configuracion_'], function () {
        Route::get('/', [AlmacenRecogidaController::class, 'getAlmacenesRecogida'])->name('get_almacenes_recogida');
        Route::get('/search', [AlmacenRecogidaController::class, 'getAlmacenesRecogidaSearch'])->name('get_almacenes_recogida_search');
        Route::post('/', [AlmacenRecogidaController::class, 'crearAlmacenRecogida'])->name('crear_almacenes_recogida');
        Route::post('/{store}/activate', [AlmacenRecogidaController::class, 'activarAlmacenRecogida'])->name('activar_almacenes_recogida');
        Route::post('/{store}/deactivate', [AlmacenRecogidaController::class, 'desactivarAlmacenRecogida'])->name('desactivar_almacenes_recogida');
        Route::group(['prefix' => '/platform/{store}'], function () {
            Route::put('/activate', [AlmacenRecogidaController::class, 'activarAlmacenCitystock'])->name('activar_almacenes_citystock');
            Route::put('/deactivate', [AlmacenRecogidaController::class, 'desactivarAlmacenCitystock'])->name('desactivar_almacenes_citystock');
            Route::put('/cnc', [AlmacenRecogidaController::class, 'toggleCandCAlmacenCitystock'])->name('toggle_cnc_almacenes_citystock');
            Route::put('/delivery', [AlmacenRecogidaController::class, 'toggleDeliveryAlmacenCitystock'])->name('toggle_delivery_almacenes_citystock');
        });
        Route::delete('/{store}/delete', [AlmacenRecogidaController::class, 'eliminarAlmacenRecogida'])->name('eliminar_almacenes_recogida');
    });

    Route::group(['prefix' => '/ajustes-devolucion'], function () {
        Route::get('/', ['as' => 'business_configuracion_get_ajustes_devolucion', 'uses' => 'Configuracion\AjustesDevolucionController@getAjustesDevolucion']);
        Route::put('/', ['as' => 'business_configuracion_put_ajustes_devolucion', 'uses' => 'Configuracion\AjustesDevolucionController@putAjustesDevolucion']);
    });

    Route::group(['prefix' => '/api'], function () {
        Route::get('/', ['as' => 'business_configuracion_get_api', 'uses' => 'Configuracion\ApiController@getApi']);
        Route::post('/generate', ['as' => 'business_configuracion_generate_api_key', 'uses' => 'Configuracion\ApiController@generateApiKey']);
        Route::delete('/delete', ['as' => 'business_configuracion_delete_api_key', 'uses' => 'Configuracion\ApiController@eliminarApiKey']);
        Route::get('/prestashop', ['as' => 'business_configuracion_api_prestashop', 'uses' => 'Configuracion\ApiController@descargarPrestashop']);
    });
});

Route::group(['prefix' => '/codigos-postales'], function () {
    Route::get('/search', ['as' => 'business_codigos_postales_search', 'uses' => 'CodigosPostalesController@search']);
});

Route::group(['prefix' => '/paises'], function () {
    Route::get('/search', ['as' => 'business_paises_search', 'uses' => 'PaisController@search']);
});

// Cuenta
Route::group(['prefix' => '/cuenta'], function () {
    Route::get('/datos-usuario', ['as' => 'business_cuenta_datos_usuario', 'uses' => 'CuentaController@getDatosUsuario']);
    Route::put('/datos-usuario', ['as' => 'business_cuenta_put_datos_usuario', 'uses' => 'CuentaController@putDatosUsuario']);
    Route::put('/datos-usuario/password', ['as' => 'business_cuenta_put_contrasena', 'uses' => 'CuentaController@putContrasena']);

    Route::get('/datos-pago', ['as' => 'business_cuenta_datos_pago', 'uses' => 'CuentaController@getDatosPago']);
    Route::get('/datos-pago/verificar', ['as' => 'business_cuenta_verificar_tarjeta', 'uses' => 'CuentaController@verificarTarjeta']);
    Route::delete('/datos-pago/{id}', ['as' => 'business_cuenta_dato_pago_delete', 'uses' => 'CuentaController@deleteDatoPago']);

    Route::get('/datos-facturacion', ['as' => 'business_cuenta_datos_facturacion', 'uses' => 'CuentaController@getDatosFacturacion']);
    Route::put('/datos-facturacion', ['as' => 'business_cuenta_put_datos_facturacion', 'uses' => 'CuentaController@putDatosFacturacion']);

    Route::get('/facturas/search', ['as' => 'business_cuenta_facturas_search', 'uses' => 'CuentaController@searchFacturas']);
    Route::get('/facturas', ['as' => 'business_cuenta_facturas', 'uses' => 'CuentaController@getFacturas']);
    Route::get('/facturas/{id}', ['as' => 'business_cuenta_factura', 'uses' => 'CuentaController@getFactura']);
    Route::get('/facturas/export/pdf', ['as' => 'business_cuenta_facturas_exportar_pdf', 'uses' => 'CuentaController@exportFacturasPdf']);
    Route::get('/facturas/export/xls', ['as' => 'business_cuenta_facturas_exportar_xls', 'uses' => 'CuentaController@exportFacturasXls']);

    Route::get('/condiciones-servicio', ['as' => 'business_cuenta_condiciones_servicio', 'uses' => 'CuentaController@getCondicionesServicio']);
    Route::get('/condiciones-servicio/aviso-legal', ['as' => 'business_cuenta_condiciones_servicio_aviso_legal', 'uses' => 'CuentaController@getAvisoLegal']);
    Route::get('/condiciones-servicio/condiciones', ['as' => 'business_cuenta_condiciones_servicio_condiciones', 'uses' => 'CuentaController@getCondiciones']);
    Route::get('/condiciones-servicio/politica-privacidad', ['as' => 'business_cuenta_condiciones_servicio_politica_privacidad', 'uses' => 'CuentaController@getPoliticaPrivacidad']);
});


Route::get('/atencion-cliente', ['as' => 'business_ayuda', 'uses' => 'AyudaController@index']);

Route::group(['prefix' => '/etiqueta'], function () {
    Route::get('/prod', ['as' => 'business_etiqueta_crear_produccion', 'uses' => 'EtiquetaController@etiquetaProduccion']);
    Route::get('/{id}', ['as' => 'business_etiqueta_crear', 'uses' => 'EtiquetaController@crearEtiqueta']);
});

Route::group(['prefix' => '/returns'], function () {
    Route::get('/{pedido_id}', ['as' => 'business_devolucion', 'uses' => 'DevolucionController@getDevolucion']);
    Route::get('/{pedido_id}/motivos', ['as' => 'business_devolucion_get_motivos', 'uses' => 'DevolucionController@getMotivos']);
    Route::post('/{pedido_id}/motivos', ['as' => 'business_devolucion_post_motivos', 'uses' => 'DevolucionController@postMotivos']);
    Route::delete('/{pedido_id}/motivos/{id}', ['as' => 'business_devolucion_delete_motivo', 'uses' => 'DevolucionController@deleteMotivo']);
    Route::get('/{pedido_id}/entrega', ['as' => 'business_devolucion_entrega', 'uses' => 'DevolucionController@getEntrega']);
    Route::post('/{pedido_id}/entrega', ['as' => 'business_devolucion_post_entrega', 'uses' => 'DevolucionController@postEntrega']);
    Route::get('/{pedido_id}/confirmar', ['as' => 'business_devolucion_confirmar', 'uses' => 'DevolucionController@getConfirmar']);
    Route::post('/{pedido_id}/confirmar', ['as' => 'business_devolucion_post_confirmar', 'uses' => 'DevolucionController@postConfirmar']);
    Route::get('/{pedido_id}/{devolucion_id}/finalizar', ['as' => 'business_devolucion_finalizar', 'uses' => 'DevolucionController@finalizar']);
});


// });