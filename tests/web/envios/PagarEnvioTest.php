<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Estado;

class PagarEnvioTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
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
