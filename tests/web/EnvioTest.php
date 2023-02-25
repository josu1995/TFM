<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Estado;

class EnvioTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNuevoEnvioRedirectALogin() {

//        $user = Usuario::find(1);
//
//        $this->actingAs($user, 'web')
//            ->visit('/transportar')
//            ->assertResponseOk();
        $this->call('POST', '/envio/nuevo', []);
        $this->assertRedirectedTo('/login');

    }

    public function testNuevoEnvioSinCamposDevuelveErrores() {

        $this->actingAs($this->user, 'web')
            ->visit('/envio/nuevo');
        $this->call('POST', '/envio/nuevo', []);
        $this->assertRedirectedTo('/envio/nuevo');
        $this->assertSessionHasErrors(['peso', 'alto', 'ancho', 'largo', 'descripcion', 'origen', 'destino', 'nombre', 'apellidos', 'email', 'cobertura', 'embalaje']);

    }

    public function testNuevoEnvioConCamposSuccess() {

        $this->actingAs($this->user, 'web')
            ->visit('/envio/nuevo');

        // Definimos el envío
        $peso = 2;
        $alto = 20.5;
        $ancho = 10.3;
        $largo = 20;
        $embalaje = 0;
        $descripcion = 'Zapatos';
        $cobertura = 1;
        $nombre = 'Test';
        $apellidos = 'Transporter';
        $email = 'test@transporter.es';
        $origen = 1;
        $destino = 2;

        // Llamamos a la creacion del envío
        $this->call('POST', '/envio/nuevo',
            ['peso' => $peso,
                'alto' => $alto,
                'ancho' => $ancho,
                'largo' => $largo,
                'embalaje' => $embalaje,
                'descripcion' => $descripcion,
                'cobertura' => $cobertura,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'origen' => $origen,
                'destino' => $destino
            ]);

        // Miramos que redirija al pago del envío
        $this->assertRedirectedTo('/envio/pago');

        // Miramos si existe el envío en BD
        $this->seeInDatabase('envios',
            ['embalaje_id' => $embalaje,
                'descripcion' => $descripcion,
                'cobertura_id' => $cobertura,
                'estado_id' => Estado::VALIDADO,
                'punto_entrega_id' => $origen,
                'punto_recogida_id' => $destino
            ]);

        // Miramos si existe el paquete en BD
        $this->seeInDatabase('paquetes',
            ['peso' => $peso,
                'alto' => $alto,
                'ancho' => $ancho,
                'largo' => $largo
            ]);

        // Miramos si existe el destinatario en BD
        $this->seeInDatabase('personas',
            ['nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email
            ]);

    }

    public function testPagoSinEnviosRedirigeAEnvios() {
        $this->actingAs($this->user, 'web')
            ->visit('/envio/pago')
            ->seePageIs('/inicio/envios');
    }

    public function testNuevoEnvioAndCheckDatosPago() {

        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::VALIDADO,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/envio/pago')
            ->see('Precio envío: <span><strong>3.00€')
            ->see('Cobertura: <span><strong>0.00€')
            ->see('Gastos de gestión: <span><strong>0.44€')
            ->see('TOTAL:  <span><strong>3.44€');

    }

//     Test para código de descuento
    public function testCodigoDescuentoSuccess() {

        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'codigo' => 'eb2-d3',
            'estado_id' => Estado::VALIDADO,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $codigo = factory(\App\Models\CodigoDescuento::class)->create([
            'id' => 1,
            'codigo' => 'testgratis',
            'unico_uso' => 0,
            'valor' => 10,
            'activo' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->call('POST', '/codigos/validate', ['codigo' => 'testgratis', 'envios' => ['eb2-d3']]);

        $this->assertSessionHas('descuento', 'validado');

    }


}
