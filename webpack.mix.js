let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    // Web styles
    .sass('resources/assets/sass/web/app.scss', 'public/css/app.css')
    .styles([
        './public/css/bootstrap.css',
        './public/css/font-awesome.css',
        './public/css/app.css',
        './public/css/stores.css',
        './public/css/app-max-768.css',
        './public/css/app-min-768.css',
        './public/css/jquery-ui.css'
    ], 'public/css/citystock.css')
    // Drivers styles
    .sass('resources/assets/sass/materialize/materialize.scss', 'public/css/drivers')
    .styles([
        './public/css/drivers/materialize.css',
        './public/css/drivers/drivers.css',
        './public/css/font-awesome.css'
    ], 'public/css/drivers/drivers-dist.css')
    // Business styles
    .sass('resources/assets/sass/business/app.scss', 'public/css/business/main.css')
    .styles([
        './public/css/bootstrap.css',
        './public/css/font-awesome.css',
        './public/css/business/AdminLTE.css',
        './public/css/business/skin-yellow.css',
        './public/css/business/main.css'
    ], 'public/css/business/business.css')
    // Devoluciones
    .sass('resources/assets/sass/devoluciones/app.scss', 'public/css/devoluciones/main.css')
    .styles([
        './public/css/bootstrap.css',
        './public/css/font-awesome.css',
        './public/css/devoluciones/main.css'
    ], 'public/css/devoluciones/devoluciones.css')
    .scripts(
        'resources/assets/js/business/configuracion/settingsStores.js',
        "public/js/business/configuracion/settingsStores.js",
    )
    .scripts('resources/assets/js/business/configuracion/createStore.js', 'public/js/business/configuracion/createStore.js')
    .scripts('resources/assets/js/business/configuracion/updateStore.js', 'public/js/business/configuracion/updateStore.js')
    .scripts('resources/assets/js/business/inventario/stocks.js', 'public/js/business/inventario/stocks.js')
    .scripts('resources/assets/js/business/inventario/movimientos.js', 'public/js/business/inventario/movimientos.js')
    .scripts('resources/assets/js/business/storesSearch.js', 'public/js/business/storesSearch.js')

    .version([
        "public/css/business/AdminLTE.css",
        "public/css/business/skin-yellow.css",
        "public/css/app.css",
        "public/css/bootstrap.css",
        "public/css/citystock.css",
        "public/css/stores.css",
        "public/css/drivers/materialize.css",
        "public/css/drivers/drivers.css",
        "public/css/drivers/register.css",
        "public/css/drivers/drivers-dist.css",
        "public/css/business/business.css",
        "public/css/devoluciones/devoluciones.css",
        "public/js/dist/jquery-bootstrap.js",
        "public/js/dist/pwstrength.js",
        "public/js/dist/qrcode.min.js",
        "public/js/dist/lazyload.min.js",
        "public/js/dist/alpine.min.js",
        "public/js/vendor/clustered.js",
        "public/js/blog/blog.js",
        "public/js/web/envio.js",
        "public/js/web/login-registro.js",
        "public/js/web/pago.js",
        "public/js/drivers/viaje.js",
        "public/js/drivers/viaje-seleccion.js",
        "public/js/drivers/viaje-pagar.js",
        "public/js/web/alerta.js",
        "public/js/drivers/drivers.js",
        "public/js/web/imagen.js",
        "public/js/web/direccion.js",
        "public/js/web/tracking.js",
        "public/js/web/stores-search.js",
        "public/js/vendor/icheck.min.js",
        "public/js/vendor/xlsx.min.js",
        "public/js/vendor/pnotify.js",
        "public/js/vendor/bootstrap-switch.min.js",
        "public/js/business/adminlte.js",
        "public/js/business/stores-search.js",
        "public/js/business/check-stores-search.js",
        "public/js/business/check-api-stores-search.js",
        "public/js/business/configuracion/checkOut.js",
        "public/js/business/configuracion/puntoRecogida.js",
        "public/js/business/configuracion/productos.js",
        "public/js/business/configuracion/paquetes.js",
        "public/js/business/configuracion/ajustesDevolucion.js",
        "public/js/business/cuenta/facturas.js",
        "public/js/business/envios/pendientesPago.js",
        "public/js/business/envios/pendientesExpedicion.js",
        "public/js/business/envios/enTransito.js",
        "public/js/business/envios/destino.js",
        "public/js/business/envios/finalizados.js",
        "public/js/business/envios/nuevo.js",
        "public/js/business/envios/importar.js",
        "public/js/business/devoluciones/pendientes.js",
        "public/js/devoluciones/motivos.js",
        "public/js/devoluciones/entrega.js",
        "public/js/devoluciones/confirmar.js",
        "public/js/business/inventario/productos.js"
    ])
    .browserSync({ proxy: '192.168.0.14', open: 'external' });
