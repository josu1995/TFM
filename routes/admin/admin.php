<?php

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

// Rutas de login
Route::get('login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\LoginController@logout']);

// Resetear contraseña
//Route::get('password/reset', ['as' => 'auth.password.reset.request', 'uses' => 'Web\Auth\ForgotPasswordController@showLinkRequestForm']);
//Route::get('password/reset/{token?}', ['as' => 'auth.password.reset', 'uses' => 'Web\Auth\ResetPasswordController@showResetForm']);
//Route::post('password/email', ['as' => 'auth.password.email',  'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);

// Portada
Route::get('/', ['as' => 'admin_inicio', 'uses' => 'AdminController@index']);
Route::get('/base-de-datos', ['as' => 'admin_esquemaDB', 'uses' => 'AdminController@esquemaDB']);
Route::get('/documentacion-API', ['as' => 'admin_api', 'uses' => 'AdminController@getAPI']);

// Usuario
Route::get('/usuarios', ['as' => 'admin_usuarios', 'uses' => 'UsuarioController@getUsuarios']);
Route::get('/usuarios/data', ['as' => 'admin_usuarios_data', 'uses' => 'UsuarioController@getUsuariosData']);
Route::get('/usuarios-puntos', ['as' => 'admin_usuarios_puntos', 'uses' => 'UsuarioController@getUsuariosPuntos']);
Route::get('/usuario/{id}', ['as' => 'admin_usuario', 'uses' => 'UsuarioController@showUsuario']);
Route::get('/usuarios/eliminados', ['as' => 'admin_usuarios_eliminados', 'uses' => 'UsuarioController@deletedUsuarios']);
Route::get('/usuarios-business', ['as' => 'admin_usuarios_business', 'uses' => 'UsuarioController@getUsuariosBusiness']);
Route::get('/usuarios-transportistas', ['as' => 'admin_usuarios_transportistas', 'uses' => 'UsuarioController@getUsuariosTransportistas']);
Route::get('/usuarios-transportistas-potenciales', ['as' => 'admin_usuarios_transportistas_potenciales', 'uses' => 'UsuarioController@getUsuariosTransportistasPotenciales']);
Route::get('/usuarios-clientes', ['as' => 'admin_usuarios_clientes', 'uses' => 'UsuarioController@getUsuariosClientes']);
Route::get('/usuarios-clientes-potenciales', ['as' => 'admin_usuarios_clientes_potenciales', 'uses' => 'UsuarioController@getUsuariosClientesPotenciales']);
Route::get('/usuarios/registro', ['as' => 'admin_usuarios_registro', 'uses' => 'UsuarioController@getUsuariosRegistro']);

// Edición completa de usuario
Route::get('/usuario/{id}/editar', ['as' => 'admin_usuario_editar', 'uses' => 'UsuarioController@editUsuario']);
Route::get('/usuario/{id}/pagos', ['as' => 'admin_usuario_pagos', 'uses' => 'UsuarioController@getUsuarioPagos']);
Route::get('/usuario', ['as' => 'admin_usuarios_nuevo_formulario', 'uses' => 'UsuarioController@storeUsuario']);
Route::post('/usuario', ['as' => 'admin_usuario_nuevo', 'uses' => 'UsuarioController@postUsuario']);
Route::post('/usuarios/registro', ['as' => 'admin_usuarios_nuevos', 'uses' => 'UsuarioController@postUsuarios']);
Route::get('/usuario-business', ['as' => 'admin_usuarios_nuevo_business', 'uses' => 'UsuarioController@getNuevoBusiness']);
Route::post('/usuario-business', ['as' => 'admin_usuarios_post_nuevo_business', 'uses' => 'UsuarioController@postNuevoBusiness']);
Route::put('/usuario/{id}/cuenta', ['as' => 'admin_usuario_actualizacion_cuenta', 'uses' => 'UsuarioController@putCuenta']);
Route::put('/usuario/{id}/password', ['as' => 'admin_usuario_actualizacion_password', 'uses' => 'UsuarioController@putPassword']);
Route::put('/usuario/{id}/roles', ['as' => 'admin_usuario_actualizacion_roles', 'uses' => 'UsuarioController@putRoles']);
Route::put('/usuario/{id}/configuracion', ['as' => 'admin_usuario_actualizacion_configuracion', 'uses' => 'UsuarioController@putConfiguracion']);
Route::put('/usuario/{id}/direccion', ['as' => 'admin_usuario_actualizacion_direccion', 'uses' => 'UsuarioController@putDireccion']);
Route::put('/usuario/{id}/cobro', ['as' => 'admin_usuario_actualizacion_cobro', 'uses' => 'UsuarioController@putCobro']);
Route::put('/usuario/{id}/restauracion', ['as' => 'admin_usuario_restaurar', 'uses' => 'UsuarioController@restoreUsuario']);
Route::delete('/usuario/{id}', ['as' => 'admin_usuario_borrar', 'uses' => 'UsuarioController@deleteUsuario']);


// Códigos de descuento
Route::get('/codigos', ['as' => 'admin_codigos', 'uses' => 'CodigosController@getCodigos']);
Route::get('/codigos/inactivos', ['as' => 'admin_codigos_inactivos', 'uses' => 'CodigosController@getCodigosInactivos']);
Route::get('/codigos/usuarios/{text}', ['as' => 'admin_codigos_find_users', 'uses' => 'CodigosController@findUsuarios']);
Route::get('/codigo', ['as' => 'admin_codigos_new_form', 'uses' => 'CodigosController@getNewCodigoForm']);
Route::post('/codigo', ['as' => 'admin_codigos_new', 'uses' => 'CodigosController@postNewCodigo']);
Route::get('/codigo/{id}', ['as' => 'admin_codigos_edit_form', 'uses' => 'CodigosController@getEditCodigoForm']);
Route::put('/codigo/{id}', ['as' => 'admin_codigos_edit', 'uses' => 'CodigosController@putCodigo']);
Route::delete('/codigo/{id}', ['as' => 'admin_codigos_delete', 'uses' => 'CodigosController@deleteCodigo']);
Route::post('/codigo/{id}/activate', ['as' => 'admin_codigos_activate', 'uses' => 'CodigosController@activate']);

// Alertas
Route::get('/alertas-puntuales', ['as' => 'admin_alertas_puntuales', 'uses' => 'AlertaController@getAlertasPuntuales']);
Route::get('/alertas-habituales', ['as' => 'admin_alertas_habituales', 'uses' => 'AlertaController@getAlertasHabituales']);
Route::get('/alertas-antiguas', ['as' => 'admin_alertas_antiguas', 'uses' => 'AlertaController@getAlertasAntiguas']);

// Emails
Route::get('/emails', ['as' => 'admin_emails', 'uses' => 'EmailController@getEmails']);
Route::get('/emails/new', ['as' => 'admin_new_email', 'uses' => 'EmailController@getNewEmail']);
Route::post('/emails/new', ['as' => 'admin_post_new_email', 'uses' => 'EmailController@postNewEmail']);
Route::get('/emails/{id}/users', ['as' => 'admin_email_users', 'uses' => 'EmailController@getEmailUsers']);
Route::get('/emails/users/find/{text}', ['as' => 'admin_email_find_users', 'uses' => 'EmailController@findUsers']);

// Stores
Route::get('/stores', ['as' => 'admin_stores', 'uses' => 'StoresController@getStores']);
Route::get('/stores/asignar', ['as' => 'admin_stores_asignar', 'uses' => 'StoresController@asignarPunto']);

// Drivers
Route::get('/drivers', ['as' => 'admin_drivers', 'uses' => 'BusinessController@getDrivers']);
Route::put('/drivers/{id}', ['as' => 'admin_drivers_put', 'uses' => 'BusinessController@putDriver']);
Route::delete('/drivers/{id}', ['as' => 'admin_drivers_delete', 'uses' => 'BusinessController@deleteDriver']);

// Business
Route::get('/business', ['as' => 'admin_business', 'uses' => 'BusinessController@getBusiness']);
Route::put('/business/{id}', ['as' => 'admin_business_put', 'uses' => 'BusinessController@putBusiness']);
Route::delete('/business/{id}', ['as' => 'admin_business_delete', 'uses' => 'BusinessController@deleteBusiness']);

// Encuestas
Route::get('/encuestas', ['as' => 'admin_encuestas', 'uses' => 'EncuestaController@getEncuestas']);
Route::get('/opiniones', ['as' => 'admin_opiniones', 'uses' => 'EncuestaController@getOpiniones']);
Route::get('/opiniones/opinion', ['as' => 'admin_nueva_opinion', 'uses' => 'EncuestaController@getNewOpinion']);
Route::post('/opiniones/opinion', ['as' => 'admin_post_nueva_opinion', 'uses' => 'EncuestaController@postNewOpinion']);
Route::get('/opiniones/opinion/{id}', ['as' => 'admin_edit_opinion', 'uses' => 'EncuestaController@getEditOpinion']);
Route::get('/opiniones/opinion/{id}/mostrar', ['as' => 'admin_mostrar_opinion', 'uses' => 'EncuestaController@mostrarOpinion']);
Route::get('/opiniones/opinion/{id}/ocultar', ['as' => 'admin_ocultar_opinion', 'uses' => 'EncuestaController@ocultarOpinion']);
Route::put('/opiniones/opinion/{id}', ['as' => 'admin_put_opinion', 'uses' => 'EncuestaController@putOpinion']);
Route::delete('/opiniones/opinion/{id}', ['as' => 'admin_delete_opinion', 'uses' => 'EncuestaController@deleteOpinion']);

// Roles
Route::get('/roles', ['as' => 'admin_configuracion_roles', 'uses' => 'RolesController@getRoles']);
Route::put('/rol/{id}', ['as' => 'admin_configuracion_roles_actualizacion', 'uses' => 'RolesController@putRol']);

// Coberturas
Route::get('/coberturas', ['as' => 'admin_configuracion_coberturas', 'uses' => 'CoberturaController@getCoberturas']);
Route::post('/cobertura', ['as' => 'admin_cobertura_nueva', 'uses' => 'CoberturaController@postCobertura']);
Route::put('/cobertura/{id}', ['as' => 'admin_cobertura_actualizar', 'uses' => 'CoberturaController@putCobertura']);

// Embalajes
Route::get('/embalajes', ['as' => 'admin_configuracion_embalajes', 'uses' => 'EmbalajeController@getEmbalajes']);
Route::post('/embalaje', ['as' => 'admin_embalaje_nueva', 'uses' => 'EmbalajeController@postEmbalaje']);
Route::put('/embalaje/{id}', ['as' => 'admin_embalaje_actualizar', 'uses' => 'EmbalajeController@putEmbalaje']);

//Calendario
Route::get('/calendario', ['as' => 'admin_configuracion_calendario', 'uses' => 'CalendarioController@getCalendario']);
Route::post('/save', ['as' => 'admin_configuracion_save_calendar', 'uses' => 'CalendarioController@saveCalendar']);
// Envios
Route::get('/envios', ['as' => 'admin_envios', 'uses' => 'EnvioController@getEnvios']);
Route::get('/envios-transito', ['as' => 'admin_envios_transito', 'uses' => 'EnvioController@getEnviosTransito']);
Route::get('/envios-destino', ['as' => 'admin_envios_destino', 'uses' => 'EnvioController@getEnviosDestino']);
Route::get('/envios-finalizados', ['as' => 'admin_envios_finalizados', 'uses' => 'EnvioController@getEnviosFinalizados']);
Route::get('/envios-retornos', ['as' => 'admin_envios_retornos', 'uses' => 'EnvioController@getEnviosRetornos']);
Route::get('/envios-devoluciones', ['as' => 'admin_envios_devoluciones', 'uses' => 'EnvioController@getEnviosDevoluciones']);
Route::get('/envios-reembolsos', ['as' => 'admin_envios_reembolsos', 'uses' => 'EnvioController@getReembolsos']);
Route::get('/envio/{codigo}', ['as' => 'admin_envio', 'uses' => 'EnvioController@showEnvio']);
Route::put('/envio/{codigo}', ['as' => 'admin_envio_actualizacion', 'uses' => 'EnvioController@putEnvio']);
Route::put('/envio/business/{codigo}', ['as' => 'admin_envio_business_actualizacion', 'uses' => 'EnvioController@putEnvioBusiness']);
Route::put('/envio/{codigo}/puntos', ['as' => 'admin_envio_actualizacion_puntos', 'uses' => 'EnvioController@putPuntos']);
Route::post('/envio/{codigo}/reembolsar', ['as' => 'admin_envio_reembolsar', 'uses' => 'EnvioController@reembolsarEnvio']);

// Viajes
Route::get('/viajes', ['as' => 'admin_viajes', 'uses' => 'ViajeController@getViajes']);
Route::get('/viajes-reserva', ['as' => 'admin_viajes_reserva', 'uses' => 'ViajeController@getViajesReserva']);
Route::get('/viajes-ruta', ['as' => 'admin_viajes_ruta', 'uses' => 'ViajeController@getViajesRuta']);
Route::get('/viajes-finalizados', ['as' => 'admin_viajes_finalizados', 'uses' => 'ViajeController@getViajesFinalizados']);
Route::get('/viajes-cancelados', ['as' => 'admin_viajes_cancelados', 'uses' => 'ViajeController@getViajesCancelados']);
Route::get('/viaje/{codigo}', ['as' => 'admin_viaje', 'uses' => 'ViajeController@getViaje']);
Route::delete('/viaje/{codigo}/cancelar', ['as' => 'admin_viaje_cancelar', 'uses' => 'ViajeController@cancelarViaje']);

// Pedidos
Route::get('/pedidos', ['as' => 'admin_pedidos', 'uses' => 'PedidoController@getPedidos']);
Route::get('/pedido/{codigo}', ['as' => 'admin_pedido', 'uses' => 'PedidoController@getPedido']);
Route::get('/pedidos/business', ['as' => 'admin_pedidos_business', 'uses' => 'PedidoController@getPedidosBusiness']);
Route::get('/pedido/{codigo}/business', ['as' => 'admin_pedido_business', 'uses' => 'PedidoController@getPedidoBusiness']);
Route::get('/pedidos/devoluciones/business', ['as' => 'admin_devoluciones_business_cliente', 'uses' => 'PedidoController@getPedidosDevolucionesBusiness']);
Route::get('/pedidos/devoluciones/{codigo}/business', ['as' => 'admin_pedido_devolucion_business', 'uses' => 'PedidoController@getPedidoDevolucionBusiness']);

// Pagos
Route::get('/pagos-efectuados', ['as' => 'admin_pagos_efectuados', 'uses' => 'PagoController@getPagosEfectuados']);
Route::get('/pagos-pendientes', ['as' => 'admin_pagos_pendientes', 'uses' => 'PagoController@getPagosPendientes']);
Route::get('/pagos-futuros', ['as' => 'admin_pagos_futuros', 'uses' => 'PagoController@getPagosFuturos']);
Route::put('/pago/{id}', ['as' => 'admin_pagos_actualizar', 'uses' => 'PagoController@putPago']);

// Ecommerces
Route::group(['prefix' => 'ecommerces/'], function () {
    Route::get('/list', ['as' => 'admin_ecommerces_list', 'uses' => 'EcommerceController@getListaEcommerce']);
    Route::get('/facturas', ['as' => 'admin_ecommerces_facturas', 'uses' => 'EcommerceController@getListaFacturas']);
    Route::get('/facturas/{id}', ['as' => 'admin_ecommerces_factura', 'uses' => 'EcommerceController@getFacturaPdf']);
    Route::get('/{id}', ['as' => 'admin_ecommerces_show', 'uses' => 'EcommerceController@show']);
    Route::get('/{id}/editar', ['as' => 'admin_ecommerces_editar', 'uses' => 'EcommerceController@getEditarEcommerce']);
    Route::put('/{id}/editar', ['as' => 'admin_ecommerces_put_editar', 'uses' => 'EcommerceController@putEditarEcommerce']);
    Route::get('/{id}/facturas', ['as' => 'admin_ecommerce_facturas', 'uses' => 'EcommerceController@getFacturasEcommerce']);
});

// Cobros
Route::get('/cobros', ['as' => 'admin_cobros_efectuados', 'uses' => 'CobroController@getCobrosEfectuados']);
Route::get('/cobrar', ['as' => 'admin_cobrar', 'uses' => 'CobroController@getCobrar']);
Route::get('/cobrar/usuarios/{mail}/tarjetas', ['as' => 'admin_cobrar_find_tarjetas', 'uses' => 'CobroController@findTarjetasUsuario']);
Route::post('/cobrar', ['as' => 'admin_post_cobrar', 'uses' => 'CobroController@postCobrar']);

// Estados
Route::get('/estados', ['as' => 'admin_configuracion_estados', 'uses' => 'EstadoController@getEstados']);
Route::put('/estados/{id}', ['as' => 'admin_configuracion_estados_actualizacion', 'uses' => 'EstadoController@putEstado']);
Route::get('/estados-viajes', ['as' => 'admin_configuracion_estados_viajes', 'uses' => 'EstadoController@getEstadosViaje']);
Route::put('/estados-viajes/{id}', ['as' => 'admin_configuracion_estados_viaje_actualizacion', 'uses' => 'EstadoController@putEstadoViaje']);

// Sliders
Route::get('sliders', ['as' => 'admin_sliders', 'uses' => 'SliderController@getSliders']);
Route::get('slider/{id}', ['as' => 'admin_slider_detalle', 'uses' => 'SliderController@getSlider']);
Route::post('slider/{id}/imagen', ['as' => 'admin_slider_imagen', 'uses' => 'SliderController@postImagen']);
Route::post('slider/{id}/imagen/{idImagen}', ['as' => 'admin_slider_imagen_update', 'uses' => 'SliderController@updateImagen']);
Route::delete('slider/{id}/imagen/{idImagen}', ['as' => 'admin_slider_imagen_delete', 'uses' => 'SliderController@deleteImagen']);
Route::post('sliders', ['as' => 'admin_slider_nuevo', 'uses' => 'SliderController@postSlider']);
Route::delete('slider', ['as' => 'admin_slider_borrar', 'uses' => 'SliderController@deleteSlider']);

// Rutas
Route::get('rutas', ['as' => 'admin_rutas', 'uses' => 'RutaController@getRutas']);
Route::post('rutas', ['as' => 'admin_ruta_nueva', 'uses' => 'RutaController@postRuta']);
Route::delete('rutas', ['as' => 'admin_ruta_borrar', 'uses' => 'RutaController@deleteRuta']);

// Localidades
Route::get('/localidades', ['as' => 'admin_localidades', 'uses' => 'LocalidadController@getLocalidades']);
Route::get('/localidad/{id}', ['as' => 'admin_localidad', 'uses' => 'LocalidadController@getLocalidad']);
Route::put('/localidad/{id}', ['as' => 'admin_localidad_actualizar', 'uses' => 'LocalidadController@putLocalidad']);
Route::get('/localidad', ['as' => 'admin_localidad_nueva_formulario', 'uses' => 'LocalidadController@storeLocalidad']);
Route::post('/localidad', ['as' => 'admin_localidad_nueva', 'uses' => 'LocalidadController@postLocalidad']);
Route::delete('/localidad', ['as' => 'admin_localidad_borrar', 'uses' => 'LocalidadController@deleteLocalidad']);

// Transportistas
Route::get('/transportistas', ['as' => 'admin_transportistas', 'uses' => 'TransportistaController@getTransportistasAdmin']);
Route::get('/transportista/show/{id}', ['as' => 'show_transportista', 'uses' => 'TransportistaController@show']);
Route::get('/transportista/aniadir', ['as' => 'aniadir_transportista', 'uses' => 'TransportistaController@aniadir']);
Route::post('/transportista/nuevo', ['as' => 'nuevo_transportista', 'uses' => 'TransportistaController@nuevo']);
Route::get('/transportista/editar/{id}', ['as' => 'editar_transportista', 'uses' => 'TransportistaController@editarTransportista']);
Route::post('/transportista/actualizar/{id}', ['as' => 'actualizar_transportista', 'uses' => 'TransportistaController@actualizar']);
Route::delete('/transportista', ['as' => 'admin_transportista_borrar', 'uses' => 'TransportistaController@deleteTransportista']);


//Metodo envio
Route::get('/metodoEnvio', ['as' => 'admin_metodoEnvio', 'uses' => 'MetodoEnvioController@getMetodosEnvioAdmin']);
Route::get('/metodoEnvio/aniadir', ['as' => 'aniadir_metodoEnvio', 'uses' => 'MetodoEnvioController@aniadir']);
Route::post('/metodoEnvio/nuevo', ['as' => 'nuevo_metodoEnvio', 'uses' => 'MetodoEnvioController@nuevo']);
Route::get('/metodoEnvio/show/{id}', ['as' => 'show_metodoEnvio', 'uses' => 'MetodoEnvioController@show']);
Route::post('/metodoEnvio/editar/{id}', ['as' => 'editar_metodoEnvio', 'uses' => 'MetodoEnvioController@editar']);
Route::post('/metodoEnvio/desplegable', ['as' => 'editar_desplegable', 'uses' => 'MetodoEnvioController@changeDesplegable']);
Route::post('/metodoEnvio/delete', ['as' => 'delete_metodoEnvio', 'uses' => 'MetodoEnvioController@delete']);
// Puntos
Route::get('/puntos', ['as' => 'admin_puntos', 'uses' => 'PuntoController@getPuntos']);
Route::get('/puntos/comisiones', ['as' => 'admin_puntos_comisiones', 'uses' => 'PuntoController@getComisionesList']);
Route::get('/puntos/stock', ['as' => 'admin_puntos_stock', 'uses' => 'PuntoController@getStockList']);
Route::get('/punto/{id}', ['as' => 'admin_punto', 'uses' => 'PuntoController@showPunto']);
Route::get('/punto/{id}/editar', ['as' => 'admin_punto_editar', 'uses' => 'PuntoController@editarPunto']);
Route::put('/punto/{id}', ['as' => 'admin_punto_actualizar', 'uses' => 'PuntoController@putPunto']);
Route::get('/punto', ['as' => 'admin_punto_nuevo_formulario', 'uses' => 'PuntoController@storePunto']);
Route::post('/punto', ['as' => 'admin_punto_nuevo', 'uses' => 'PuntoController@postPunto']);
Route::post('/punto/{id}/imagen', ['as' => 'admin_punto_imagen', 'uses' => 'PuntoController@postImagen']);
Route::post('/punto/{id}/horario', ['as' => 'admin_punto_horario', 'uses' => 'PuntoController@postHorario']);
Route::delete('/punto/{id}/horario/{idHorario}', ['as' => 'admin_punto_horario_borrar', 'uses' => 'PuntoController@deleteHorario']);
Route::get('/punto/{id}/comisiones', ['as' => 'admin_punto_comisiones', 'uses' => 'PuntoController@getComisiones']);
Route::post('/punto/{id}/comisiones', ['as' => 'admin_punto_comisiones_pagar', 'uses' => 'PuntoController@postComisiones']);
Route::delete('/punto/{id}/comisiones', ['as' => 'admin_punto_comisiones_eliminar', 'uses' => 'PuntoController@deleteComisiones']);
Route::get('/punto/{id}/envios', ['as' => 'admin_punto_envios', 'uses' => 'PuntoController@getEnvios']);
Route::get('/punto/{id}/stock', ['as' => 'admin_punto_stock', 'uses' => 'PuntoController@getStock']);
Route::put('/punto/{id}/stock', ['as' => 'admin_punto_put_stock', 'uses' => 'PuntoController@putStock']);
Route::get('/punto/{id}/stock/gastos', ['as' => 'admin_punto_stock_gastos', 'uses' => 'PuntoController@getGastosTable']);

Route::delete('/punto/{id}', ['as' => 'admin_punto_eliminar', 'uses' => 'PuntoController@deletePunto']);
Route::put('/punto/{id}/restaurar', ['as' => 'admin_punto_restaurar', 'uses' => 'PuntoController@restorePunto']);
Route::get('/puntos/eliminados', ['as' => 'admin_puntos_eliminados', 'uses' => 'PuntoController@getEliminados']);

// Categorias
Route::get('/categorias', ['as' => 'admin_categorias', 'uses' => 'CategoriaController@getCategorias']);
Route::post('/categoria', ['as' => 'admin_categoria_nueva', 'uses' => 'CategoriaController@postCategoria']);
Route::get('/categoria/{id}', ['as' => 'admin_categoria', 'uses' => 'CategoriaController@getCategoria']);
Route::delete('/categoria/{id}', ['as' => 'admin_categoria_eliminar', 'uses' => 'CategoriaController@deleteCategoria']);
Route::put('/categoria/{id}', ['as' => 'admin_categoria_actualizar', 'uses' => 'CategoriaController@putCategoria']);

// Páginas estáticas
Route::get('/paginas', ['as' => 'admin_paginas', 'uses' => 'PaginaController@getPaginas']);
Route::get('/paginas/papelera', ['as' => 'admin_pagina_papelera', 'uses' => 'PaginaController@getDeletedPaginas']);
Route::get('/pagina/nueva', ['as' => 'admin_pagina_nueva', 'uses' => 'PaginaController@createPagina']);
Route::post('/pagina', ['as' => 'admin_pagina_crear', 'uses' => 'PaginaController@postPagina']);
Route::delete('/pagina/{id}', ['as' => 'admin_pagina_eliminar', 'uses' => 'PaginaController@deletePagina']);
Route::get('/pagina/{id}', ['as' => 'admin_pagina_actualizar', 'uses' => 'PaginaController@updatePagina']);
Route::put('/pagina/{id}', ['as' => 'admin_pagina_actualizacion', 'uses' => 'PaginaController@putPagina']);
Route::put('/pagina/{id}/publicar', ['as' => 'admin_pagina_publicar', 'uses' => 'PaginaController@putPublicar']);
Route::get('/footer-links', ['as' => 'admin_footer_links', 'uses' => 'FooterLinkController@getLinks']);
Route::get('/footer-links/new', ['as' => 'admin_new_footer_link', 'uses' => 'FooterLinkController@getNewLink']);
Route::post('/footer-links/new', ['as' => 'admin_post_new_footer_link', 'uses' => 'FooterLinkController@postNewLink']);
Route::get('/footer-links/{id}', ['as' => 'admin_edit_footer_link', 'uses' => 'FooterLinkController@getLink']);
Route::put('/footer-links/{id}', ['as' => 'admin_put_footer_link', 'uses' => 'FooterLinkController@putLink']);
Route::delete('/footer-links/{id}', ['as' => 'admin_delete_footer_link', 'uses' => 'FooterLinkController@deleteLink']);
Route::put('/footer-links/{id}/restore', ['as' => 'admin_restore_footer_link', 'uses' => 'FooterLinkController@restoreLink']);

// Configuración general Transporter
Route::get('/configuracion', ['as' => 'admin_configuracion', 'uses' => 'OpcionController@getOpciones']);
Route::post('/configuracion', ['as' => 'admin_nueva_opcion', 'uses' => 'OpcionController@postOpcion']);
Route::get('/configuracion/actualizar', ['as' => 'admin_configuracion_opciones_actualizar', 'uses' => 'OpcionController@editOpciones']);
Route::put('/configuracion/{id}/actualizar', ['as' => 'admin_opciones_actualizacion', 'uses' => 'OpcionController@putOpcion']);

Route::group(['prefix' => 'configuracion/rangos'], function () {
    Route::get('/envio', ['as' => 'admin_configuracion_rangos_envios', 'uses' => 'OpcionController@getRangosEnviosPeso']);
    Route::post('/envio', ['as' => 'admin_configuracion_rangos_envios_nuevo', 'uses' => 'OpcionController@postRangosEnviosPeso']);
    Route::delete('/{id}', ['as' => 'admin_configuracion_rangos_envios_eliminar', 'uses' => 'OpcionController@deleteRangosEnviosPeso']);
});

Route::group(['prefix' => 'configuracion/precios-business'], function () {
    Route::get('/', ['as' => 'admin_configuracion_precios_business', 'uses' => 'OpcionController@getPreciosBusiness']);
    Route::post('/', ['as' => 'admin_configuracion_post_tarifa_business', 'uses' => 'OpcionController@postTarifaBusiness']);
    Route::put('/', ['as' => 'admin_configuracion_put_tarifa_business', 'uses' => 'OpcionController@putTarifaBusiness']);
    Route::delete('/rango/{id}', ['as' => 'admin_configuracion_delete_rango_business', 'uses' => 'OpcionController@deleteRangoBusiness']);
    Route::post('/rango', ['as' => 'admin_configuracion_post_rango_business', 'uses' => 'OpcionController@postRangoBusiness']);
});

Route::group(['prefix' => 'configuracion/zonas-business'], function () {
    Route::get('/', ['as' => 'admin_configuracion_zonas_business', 'uses' => 'OpcionController@getZonasBusiness']);
    Route::post('/', ['as' => 'admin_configuracion_post_zona_business', 'uses' => 'OpcionController@postZonaBusiness']);
    Route::put('/{id}', ['as' => 'admin_configuracion_put_zona_business', 'uses' => 'OpcionController@putZonaBusiness']);
    Route::delete('/{id}', ['as' => 'admin_configuracion_delete_zona_business', 'uses' => 'OpcionController@deleteZonaBusiness']);
});

// Chart tables
Route::group(['prefix' => 'chats/tables'], function () {
    Route::get('/usuarios', ['as' => 'admin_chart_usuarios', 'uses' => 'ChartController@getUsuarios']);
    Route::get('/envios', ['as' => 'admin_chart_envios', 'uses' => 'ChartController@getEnvios']);
});

// Facturas
Route::get('/facturas/{pedido_id}', ['as' => 'admin_generar_factura', 'uses' => 'FacturaController@getFactura']);
Route::get('/facturas/stores/{store_id}/fecha/{fecha}', ['as' => 'admin_generar_factura_store', 'uses' => 'FacturaController@getFacturaStore']);
Route::get('/facturas/viajes/{viaje_id}', ['as' => 'admin_generar_factura_transportista', 'uses' => 'FacturaController@getFacturaTransportista']);
Route::get('/facturas/devoluciones/business/{devolucion_id}', ['as' => 'admin_generar_factura_devolucion_business', 'uses' => 'FacturaController@getFacturaDevolucionBusiness']);