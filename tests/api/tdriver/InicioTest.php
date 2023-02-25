<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Configuracion;

class InicioTest extends TestCase
{
    use DatabaseTransactions;
    private $user;
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNTk4NjAsImV4cCI6MTUxMDU2OTQ2MCwibmJmIjoxNTA5MzU5ODYwLCJqdGkiOiJvV2pYQUdFRGp6TnZwbXMzIn0.u8iepWlI-dpYWCR1V6w1pvcjUmd5nLfDni2I1kjKQGc';
    private $validToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNzMyNjQsImV4cCI6MTUxMDU4Mjg2NCwibmJmIjoxNTA5MzczMjY0LCJqdGkiOiJiV3dEcTFCZWtMMnZsQXg0In0.OaGpB4NDPyob5NjQSSCOJ5xnacyblDWhhbNMGck7ABY';


    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    // INICIO

    /**
     * Inicio success test.
     *
     * @return String json retrieving all inicio data
     */
    public function testInicioSuccess() {

        $alerta = factory(\App\Models\Alerta::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_alertas_id' => 1,
            'origen_id' => 1,
            'destino_id' => 2
        ]);

        $envioRuta = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envioRuta->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envioRuta->viajes()->save(
            factory(\App\Models\Viaje::class)->make([
                'id' => 1,
                'base' => 3,
                'impuestos' => 0.65,
                'gestion' => 0.25,
                'transportista_id' => 1,
                'estado_fianza' => 1,
                'estado_id' => 1
            ])
        );

        $envioHistorial = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 9,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envioHistorial->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envioHistorial->viajes()->save(
            factory(\App\Models\Viaje::class)->make([
                'id' => 2,
                'base' => 3,
                'impuestos' => 0.65,
                'gestion' => 0.25,
                'transportista_id' => 1,
                'estado_fianza' => 1,
                'estado_id' => 3
            ])
        );

        $envioHistorial->pagos()->save(
            factory(\App\Models\Pago::class)->make([
                'id' => 1,
                'envio_id' => 2,
                'viaje_id' => 2,
                'usuario_id' => 1,
                'valor' => 3.90,
                'estado_pago' => 0
            ])
        );

        $envioHistorial2 = factory(\App\Models\Envio::class)->create([
            'id' => 3,
            'precio' => 3.90,
            'estado_id' => 9,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envioHistorial2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envioHistorial2->viajes()->save(
            factory(\App\Models\Viaje::class)->make([
                'id' => 3,
                'base' => 3,
                'impuestos' => 0.65,
                'gestion' => 0.25,
                'transportista_id' => 1,
                'estado_fianza' => 1,
                'estado_id' => 3
            ])
        );

        $envioHistorial2->pagos()->save(
            factory(\App\Models\Pago::class)->make([
                'id' => 2,
                'envio_id' => 3,
                'viaje_id' => 3,
                'usuario_id' => 1,
                'valor' => 3.90,
                'estado_pago' => 1,
                'fecha_pago' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
            ])
        );

        $mensajes = factory(\App\Models\Mensaje::class, 4)->create([
            'usuario_id' => 1,
            'envio_id' => 1,
        ]);

        $datosFacturacion = factory(\App\Models\DatosFacturacion::class)->create([
            'usuario_id' => 1,
        ]);

        $imagen = factory(\App\Models\Imagen::class)->create([
            'usuario_id' => 1,
        ]);

        $vehiculo = factory(\App\Models\Vehiculo::class)->create([
            'usuario_id' => 1,
        ]);

        $this->user->metodosCobro()->save(
            factory(\App\Models\MetodoCobro::class)->make([
                'usuario_id' => 1,
                'tipo_metodo_id' => 3,
                'email' => null,
                'defecto' => 1,
            ])
        );

        $this->json('GET', '/api/tdriver/v1/users/1/home', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([
                'alertas' => ['*' => ['id', 'created_at', 'tipo_alertas_id', 'origen', 'destino', 'dias', 'fecha']],
                'viajes_ruta' => ['*' => ['id', 'created_at', 'codigo', 'origen', 'destino',
                    'envios' => ['*' => ['id', 'localizador', 'estado_id',
                        'punto_entrega' => ['id', 'nombre', 'direccion', 'latitud', 'longitud',
                            'localidad' => ['id', 'nombre']],
                        'punto_recogida' => ['id', 'nombre', 'direccion', 'latitud', 'longitud',
                            'localidad' => ['id', 'nombre'],
                            'horarios' => ['*' => ['dia', 'inicio', 'fin', 'cerrado', 'punto_id']]],
                        'paquete' => ['peso', 'ancho', 'largo', 'alto']
                    ]]
                ]],
                'viajes_historial' => ['*' => ['id', 'importe', 'created_at', 'fecha_finalizacion',
                    'envios' => ['*' => ['id', 'store_origen', 'store_destino',
                        'paquete' => ['peso', 'ancho', 'largo', 'alto']
                    ]]
                ]],
                'mensajes' => ['*' => ['id', 'texto', 'leido', 'envio_id', 'created_at']],
                'cuenta' => [
                    'datos' => ['nombre', 'apellidos', 'fecha_nacimiento', 'dni'],
                    'imagen',
                    'certificaciones' => ['mail', 'movil'],
                    'vehiculo' => ['matricula', 'marca', 'modelo', 'tarjeta_transporte', 'seguro_mercancias'],
                    'ingresos' => [
                        'pendientes' => ['*' => ['id', 'viaje_id', 'valor', 'estado_pago', 'created_at',
                            'viaje' => ['id', 'fecha_finalizacion', 'origen', 'destino']]],
                        'cobrados' => ['*' => ['id', 'viaje_id', 'valor', 'estado_pago', 'fecha_pago',
                            'viaje' => ['id', 'fecha_finalizacion', 'origen', 'destino']]]
                    ],
                    'datos_facturacion' => ['razon_social', 'nif', 'direccion', 'codigo_postal', 'ciudad', 'recibo'],
                    'cuenta_bancaria' => ['titular', 'domiciliacion', 'iban']
                ],
                'rating' => ['stars', 'viajes_completados', 'cancelaciones', 'ratio_incidencias']
            ])
            ->assertResponseStatus(200);
    }

    /**
    * Inicio success no alertas test.
    *
    * @return String json retrieving inicio data
    */
    public function testInicioNoDataSuccess() {

        Configuracion::where('usuario_id', $this->user->id)->update(['nombre' => null, 'apellidos' => null, 'fecha_nacimiento' => null, 'dni' => null]);

        $this->json('GET', '/api/tdriver/v1/users/1/home', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonEquals([
                'alertas' => [],
                'viajes_ruta' => [],
                'viajes_historial' => [],
                'mensajes' => [],
                'cuenta' => [
                    'datos' => ['nombre' => null, 'apellidos' => null, 'fecha_nacimiento' => null, 'dni' => null],
                    'imagen' => null,
                    'certificaciones' => ['mail' => 1, 'movil' => 1],
                    'vehiculo' => null,
                    'ingresos' => [
                        'pendientes' => [],
                        'cobrados' => []
                    ],
                    'datos_facturacion' => null,
                    'cuenta_bancaria' => null
                ],
                'rating' => ['stars' => 5, 'viajes_completados' => 0, 'cancelaciones' => 0, 'ratio_incidencias' => 0]
            ])
            ->assertResponseStatus(200);
    }


}
