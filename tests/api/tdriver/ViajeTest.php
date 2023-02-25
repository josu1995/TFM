<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Configuracion;

class ViajeTest extends TestCase
{
    use DatabaseTransactions;
    private $user;
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNTk4NjAsImV4cCI6MTUxMDU2OTQ2MCwibmJmIjoxNTA5MzU5ODYwLCJqdGkiOiJvV2pYQUdFRGp6TnZwbXMzIn0.u8iepWlI-dpYWCR1V6w1pvcjUmd5nLfDni2I1kjKQGc';
    private $validToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNzMyNjQsImV4cCI6MTUxMDU4Mjg2NCwibmJmIjoxNTA5MzczMjY0LCJqdGkiOiJiV3dEcTFCZWtMMnZsQXg0In0.OaGpB4NDPyob5NjQSSCOJ5xnacyblDWhhbNMGck7ABY';


    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    // SEARCH CITIES

    /**
     * Inicio success test.
     *
     * @return String json retrieving all inicio data
     */
    public function testSearchCitiesByTextSuccess() {

        $this->json('GET', '/api/tdriver/v1/cities/search?text=ma', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonEquals([
                [ 'id' => 4, 'nombre' => 'Madrid' ],
                [ 'id' => 7, 'nombre' => 'Málaga' ]
            ])
            ->assertResponseStatus(200);
    }

    public function testSearchCitiesNoParams() {

        $this->json('GET', '/api/tdriver/v1/cities/search', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testSearchCitiesByTextNoCities() {

        $this->json('GET', '/api/tdriver/v1/cities/search?text=opa', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testSearchCitiesByLocationSuccess() {

        $this->json('GET', '/api/tdriver/v1/cities/search?lat=43.262676&lon=-2.935116', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonEquals([ 'id' => 3, 'nombre' => 'Bilbao' ])
            ->assertResponseStatus(200);
    }

    public function testSearchCitiesByBadLocationError() {

        $this->json('GET', '/api/tdriver/v1/cities/search?lat=500000.262676&lon=-2.935116', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message', 'nearest' ])
            ->assertResponseStatus(207);
    }

    public function testSearchCitiesByLatError() {

        $this->json('GET', '/api/tdriver/v1/cities/search?lat=43.262676', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testSearchCitiesByLonError() {

        $this->json('GET', '/api/tdriver/v1/cities/search?lon=43.262676', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testSearchCitiesByNoTextError() {

        $this->json('GET', '/api/tdriver/v1/cities/search?text=', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testSearchCitiesByNoLocationError() {

        $this->json('GET', '/api/tdriver/v1/cities/search?lat=&lon=', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    // STORES ORIGEN

    public function testGetStoresOrigenSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $this->json('GET', '/api/tdriver/v1/cities/3/stores?destination=11', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                    'id' => 1,
                    'nombre' => 'Punto de Bilbao',
                    'id' => 4,
                    'nombre' => 'Punto de Bilbao 2'
                ])
            ->assertResponseStatus(200);

    }

    public function testGetStoresOrigenNoArgsError() {

        $this->json('GET', '/api/tdriver/v1/cities/3/stores', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresOrigenNoDestinationError() {

        $this->json('GET', '/api/tdriver/v1/cities/3/stores?destination=', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresOrigenNoCityError() {

        $this->json('GET', '/api/tdriver/v1/cities/25/stores?destination=11', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresOrigenNoEnviosError() {

        $this->json('GET', '/api/tdriver/v1/cities/3/stores?destination=11', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    // STORES DESTINO

    public function testGetStoresDestinoSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 4,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );


        $this->json('GET', '/api/tdriver/v1/cities/11/stores?stores=1,4', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                'id' => 2,
                'nombre' => 'Punto de Alicante',
                'id' => 5,
                'nombre' => 'Punto de Alicante 2'
            ])
            ->assertResponseStatus(200);
    }

    public function testGetStoresDestinoNoStoresError() {

        $this->json('GET', '/api/tdriver/v1/cities/11/stores?stores=', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresDestinoNoCityError() {

        $this->json('GET', '/api/tdriver/v1/cities/25/stores?stores=1,4', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresDestinoNoEnviosError() {

        $this->json('GET', '/api/tdriver/v1/cities/11/stores?stores=1,4', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);

    }

    public function testGetStoresDestinoBadRouteError() {

        $this->json('GET', '/api/tdriver/v1/cities/25/stores?stores=2,3', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure([ 'message' ])
            ->assertResponseStatus(400);
    }

    public function testGetStoresDestinoIntermediosSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 4,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );


        $this->json('GET', '/api/tdriver/v1/cities/4/stores?stores=1,4', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                'id' => 3,
                'nombre' => 'Punto de Madrid'
            ])
            ->assertResponseStatus(200);
    }

    // VER PAQUETES

    public function testGetPaquetesSuccess() {
        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 4,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $this->json('GET', '/api/tdriver/v1/users/1/travel?origins=1,4&destinations=3', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['localidad_origen', 'localidad_destino', 'envios'])
            ->assertResponseStatus(200);
    }

    public function testGetPaquetesIntermediosSuccess() {

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 7,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0
        ]);

        $envio1->posiciones()->save(
            factory(\App\Models\Posicion::class)->make([
                'id' => 1,
                'viaje_id' => 1,
                'envio_id' => 1,
                'punto_origen_id' => null,
                'punto_destino_id' => 3,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ])
        );

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $this->json('GET', '/api/tdriver/v1/users/1/travel?origins=3&destinations=2', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['localidad_origen', 'localidad_destino', 'envios'])
            ->assertResponseStatus(200);
    }

    public function testGetPaquetesNoPaquetesError() {

        $this->json('GET', '/api/tdriver/v1/users/1/travel?origins=1,4&destinations=2', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testGetPaquetesNoArgsError() {

        $this->json('GET', '/api/tdriver/v1/users/1/travel', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testGetPaquetesNonExistentStoresError() {

        $this->json('GET', '/api/tdriver/v1/users/1/travel?origins=98999,73774&destinations=8387477,929239', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testGetPaquetesNoDestinationsError() {

        $this->json('GET', '/api/tdriver/v1/users/1/travel?origins=1', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testGetPaquetesNoOriginsError() {

        $this->json('GET', '/api/tdriver/v1/users/1/travel?destinations=1', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    // RESERVAR PAQUETES

    public function testReservarPaquetesSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 1
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 4,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 2
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $destinatario = factory(\App\Models\Persona::class)->create([
            'id' => 1,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $destinatario2 = factory(\App\Models\Persona::class)->create([
            'id' => 2,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [1,4], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                'origen' => 'Bilbao',
                'destino' => 'Alicante',
                'id' => 1,
                'id' => 2])
            ->assertResponseStatus(200);
    }

    public function testReservarPaquetesAIntermedioSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 1
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 4,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 2
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $destinatario = factory(\App\Models\Persona::class)->create([
            'id' => 1,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $destinatario2 = factory(\App\Models\Persona::class)->create([
            'id' => 2,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [1,4], 'destinations' => [3], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                'origen' => 'Bilbao',
                'destino' => 'Madrid',
                'id' => 1,
                'id' => 2])
            ->assertResponseStatus(200);
    }

    public function testReservarPaquetesDesdeIntermedioSuccess() {

        $puntoBilbao2 = factory(\App\Models\Punto::class)->create([
            'id' => 4,
            'nombre' => 'Punto de Bilbao 2',
            'direccion' => 'Dirección de Punto de Bilbao 2',
            'telefono' => 912345678,
            'codigo_postal' => 48012,
            'horario' => '',
            'localidad_id' => 3,
            'latitud' => '43.262976',
            'longitud' => '-2.940322',
            'usuario_id' => 2
        ]);

        $puntoAlicante2 = factory(\App\Models\Punto::class)->create([
            'id' => 5,
            'nombre' => 'Punto de Alicante 2',
            'direccion' => 'Dirección de Punto de Alicante 2',
            'telefono' => 912345678,
            'codigo_postal' => 03005,
            'horario' => '',
            'localidad_id' => 11,
            'latitud' => '38.351467',
            'longitud' => '-0.487380',
            'usuario_id' => 3
        ]);

        $envio1 = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'precio' => 3.90,
            'estado_id' => 7,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 1
        ]);

        $envio1->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $envio1->posiciones()->save(
            factory(\App\Models\Posicion::class)->make([
                'id' => 1,
                'viaje_id' => 1,
                'envio_id' => 1,
                'punto_origen_id' => null,
                'punto_destino_id' => 3,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ])
        );

        $envio2 = factory(\App\Models\Envio::class)->create([
            'id' => 2,
            'precio' => 3.90,
            'estado_id' => 5,
            'punto_recogida_id' => 5,
            'punto_entrega_id' => 3,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'destinatario_id' => 2
        ]);

        $envio2->paquete()->save(
            factory(\App\Models\Paquete::class)->make()
        );

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $destinatario = factory(\App\Models\Persona::class)->create([
            'id' => 1,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $destinatario2 = factory(\App\Models\Persona::class)->create([
            'id' => 2,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJson([
                'origen' => 'Madrid',
                'destino' => 'Alicante',
                'id' => 1,
                'id' => 2])
            ->assertResponseStatus(200);
    }

    public function testReservarPaquetesNoEnviosError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [], 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesInvalidEnviosError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 2, 5 ], 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesStringEnviosError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => '2,5', 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesNoArgsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', [], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesNoOriginsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesInvalidOriginsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [99992], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesStringOriginsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => '99992', 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesNoDestinationsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesInvalidDestinationsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [23848], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesStringDestinationsError() {

        $metodoCobro = factory(\App\Models\MetodoCobro::class)->create([
            'id' => 1,
            'usuario_id' => 1,
            'tipo_metodo_id' => 3,
            'titular' => 'Test',
            'domiciliacion' => 'Transporter',
            'email' => null,
            'defecto' => 1,
        ]);

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => '23848', 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesNoMethodError() {

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesInvalidMethodError() {

        $this->json('POST', '/api/tdriver/v1/users/1/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [2,5], 'method' => 'AS' ], ['authorization' => 'Bearer' . $this->validToken])
            ->seeJsonStructure(['message'])
            ->assertResponseStatus(400);
    }

    public function testReservarPaquetesInvalidUserIdError() {

        $this->json('POST', '/api/tdriver/v1/users/2/viajes', ['shipments' => [ 1, 2 ], 'origins' => [3], 'destinations' => [2,5], 'method' => 1 ], ['authorization' => 'Bearer' . $this->validToken])
            ->assertResponseStatus(401);
    }


}
