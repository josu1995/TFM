<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\Usuario::class, function (Faker\Generator $faker) {
    return [
        'usuario' => $faker->username,
        'email' => $faker->email,
        'identificador' => $faker->uuid,
        'password' => bcrypt('invbit'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Models\Configuracion::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->firstName,
        'apellidos' => $faker->lastName,
        'telefono' => $faker->phoneNumber,
        'fecha_nacimiento' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'movil_certificado' => 0,
        'mail_certificado' => 0
    ];
});

$factory->define(App\Models\ConfiguracionBusiness::class, function (Faker\Generator $faker) {
    return [
        'tarifa_id' => 1,
        'razon_social' => $faker->name,
        'direccion' => $faker->address,
        'nif' => $faker->name,
        'codigo_postal' => $faker->postcode,
        'ciudad' => $faker->city,
        'nombre_comercial' => $faker->name,
        'web' => $faker->url,
        'tipo_negocio_id' => \App\Models\BusinessRegistroTipoNegocio::TECNOLOGIA_ELECTRONICA,
        'tienda_online_id' => \App\Models\BusinessRegistroTiendaOnline::PRESTASHOP,
        'marketplaces_id' => \App\Models\BusinessRegistroMarketplaces::NO_MARKETPLACES,
        'api_key' => $faker->randomAscii
    ];
});

$factory->define(App\Models\AjustesDevolucionBusiness::class, function (Faker\Generator $faker) {
    return [
        'plazo' => 14,
        'color' => $faker->hexColor,
        'opcion_etiqueta_id' => \App\Models\OpcionEtiquetaDevolucionBusiness::PREIMPRESA,
        'opcion_store' => 1,
        'opcion_domicilio' => 0,
        'opcion_coste_id' => \App\Models\OpcionCosteDevolucionBusiness::PREPAGADO
    ];
});

$factory->define(App\Models\PreferenciaRecogidaBusiness::class, function (Faker\Generator $faker) {
    return [
        'tipo_recogida_id' => \App\Models\TiposRecogidaBusiness::DOMICILIO,
        'cp_id' => 1,
        'direccion' => $faker->address,
        'tipo_solicitud_recogida' => 1,
        'dias' => 'L,M',
        'franja_horaria_id' => 1
    ];
});

$factory->define(App\Models\Punto::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->company,
        'direccion' => $faker->address,
        'telefono' => $faker->phoneNumber,
        'codigo_postal' => $faker->postcode,
        'horario' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'localidad_id' => $faker->numberBetween($min = 1, $max = 15),
        'latitud' => $faker->latitude($min = -44.0, $max = -42),
        'longitud' => $faker->longitude($min = 2.3, $max = 4),
        'usuario_id' => 1
    ];
});

$factory->define(App\Models\Localidad::class, function (Faker\Generator $faker) {
    return [
        'provincia_id' => $faker->numberBetween($min = 1, $max = 52),
        'nombre' => $faker->city,
        'nombre_seo' => $faker->city,
        'codigo_postal' => $faker->postcode,
        'latitud' => $faker->latitude($min = -44.0, $max = -42),
        'longitud' => $faker->longitude($min = 2.3, $max = 4),
    ];
});

$factory->define(App\Models\Posicion::class, function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'viaje_id' => 1,
        'envio_id' => 1,
        'punto_origen_id' => 1,
        'punto_destino_id' => 2,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ];
});

$factory->define(App\Models\CodigoDescuento::class, function (Faker\Generator $faker) {
    return [
        'codigo' => str_random(10),
        'unico_uso' => $faker->numberBetween($min = 0, $max = 1),
        'valor' => $faker->numberBetween($min = 1, $max = 100),
        'activo' => $faker->numberBetween($min = 0, $max = 1),
    ];
});

$factory->define(App\Models\Envio::class, function (Faker\Generator $faker) {
    return [
        'descripcion' => str_random(10),
        'codigo' => $faker->uuid,
        'precio' => 0.00,
        'estado_id' => $faker->numberBetween($min = 1, $max = 11),
        'punto_recogida_id' => $faker->randomNumber(),
        'punto_entrega_id' => $faker->randomNumber(),
        'cobertura_id' => 1,
        'embalaje_id' => 0,
    ];
});

$factory->define(App\Models\Paquete::class, function (Faker\Generator $faker) {
    return [
        'peso' => $faker->randomFloat(2, 1, 20),
        'alto' => $faker->randomFloat(2, 1, 50),
        'ancho' => $faker->randomFloat(2, 1, 50),
        'largo' => $faker->randomFloat(2, 1, 50),
    ];
});

$factory->define(App\Models\Persona::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->firstName,
        'apellidos' => $faker->lastName,
        'email' => $faker->email,
        'telefono' => $faker->phoneNumber,
        'dni' => '11111111H'
    ];
});

$factory->define(App\Models\Alerta::class, function (Faker\Generator $faker) {
    return [
        'usuario_id' => 1,
        'tipo_alertas_id' => 1,
        'origen_id' => 1,
        'destino_id' => 2,
        'fecha' => $faker->date('Y-m-d'),
        'activo' => 1,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\Viaje::class, function (Faker\Generator $faker) {
    return [
        'base' => 1,
        'impuestos' => 1,
        'gestion' => 1,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'transportista_id' => 1,
        'codigo' => $faker->uuid,
        'estado_fianza' => 1,
        'estado_id' => $faker->numberBetween($min = 1, $max = 4),
        'fecha_ruta' => $faker->dateTime,
        'fecha_finalizacion' => $faker->dateTime
    ];
});

$factory->define(App\Models\Pago::class, function (Faker\Generator $faker) {
    return [
        'envio_id' => 1,
        'viaje_id' => 1,
        'valor' => $faker->randomFloat(2, 1, 5),
        'estado_pago' => $faker->numberBetween(0,1),
        'fecha_pago' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\Mensaje::class, function (Faker\Generator $faker) {
    return [
        'texto' => $faker->sentence,
        'leido' => $faker->numberBetween(0,1),
        'usuario_id' => 1,
        'envio_id' => 1,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\Vehiculo::class, function (Faker\Generator $faker) {
    return [
        'usuario_id' => 1,
        'matricula' => $faker->text,
        'marca' => $faker->text,
        'modelo' => $faker->text,
        'tarjeta_transporte' => $faker->text,
        'seguro_mercancias' => $faker->text,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\DatosFacturacion::class, function (Faker\Generator $faker) {
    return [
        'usuario_id' => 1,
        'razon_social' => $faker->text,
        'nif' => '11111111H',
        'direccion' => $faker->address,
        'codigo_postal' => $faker->postcode,
        'ciudad' => $faker->city,
        'recibo' => $faker->text,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\Imagen::class, function (Faker\Generator $faker) {
    return [
        'titulo' => $faker->sentence,
        'descripcion' => $faker->sentence,
        'path' => $faker->sentence,
        'usuario_id' => 1,
        'punto_id' => null,
        'slider_id' => null,
        'order' => null,
        'blog' => 0,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\MetodoCobro::class, function (Faker\Generator $faker) {
    return [
        'usuario_id' => 1,
        'tipo_metodo_id' => 1,
        'titular' => $faker->firstName,
        'domiciliacion' => $faker->text,
        'iban' => \Illuminate\Support\Facades\Crypt::encrypt($faker->iban('ES')),
        'email' => $faker->email,
        'defecto' => $faker->numberBetween(0, 1),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(App\Models\Pedido::class, function (Faker\Generator $faker) {
    return [
        'identificador' => $faker->uuid,
        'base' => 1,
        'embalajes' => 0,
        'coberturas' => 0,
        'gestion' => 0,
        'descuento' => 0,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'estado_pago_id' => $faker->numberBetween(1,4),
        'metodo_id' => $faker->numberBetween(1,4),
        'usuario_id' => 1
    ];
});
