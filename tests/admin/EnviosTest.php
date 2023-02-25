<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Estado;

class EnviosTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    public function testEnviosListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios');
        $this->assertResponseStatus(200);
    }

    public function testTransitoListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-transito');
        $this->assertResponseStatus(200);
    }

    public function testDestinoListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-destino');
        $this->assertResponseStatus(200);
    }

    public function testDevolucionesListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-devoluciones');
        $this->assertResponseStatus(200);
    }

    // Envíos en tránsito

    public function testTiempoTransito() {
        // En verde salen los envíos que llevan menos de 4 días desde entrega en origen
        $subdays = rand(0,10);
        $subhours = rand(0,23);
        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::ENTREGA,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1,
            'fecha_almacen' => \Carbon\Carbon::now()->subDays($subdays)->subHour($subhours)
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-transito')
            ->see($subdays . 'd ' . $subhours . 'h 0m');

    }

    public function testTransitoSeleccionado() {
        // En gris salen los envíos pagados que no han sido entregados en origen
        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::SELECCIONADO,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1,
            'fecha_almacen' => \Carbon\Carbon::now()->subDays(3)->subHour(2)
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-transito')
            ->see('3d 2h 0m');

    }

    public function testTransitoRuta() {
        // En gris salen los envíos pagados que no han sido entregados en origen
        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::RUTA,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1,
            'fecha_almacen' => \Carbon\Carbon::now()->subDays(3)->subHour(2)
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-transito')
            ->see('3d 2h 0m');

    }

    public function testTransitoIntermedio() {
        // En gris salen los envíos pagados que no han sido entregados en origen
        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::INTERMEDIO,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1,
            'fecha_almacen' => \Carbon\Carbon::now()->subDays(3)->subHour(2)
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-transito')
            ->see('3d 2h 0m');

    }

    // Envíos en destino

    public function testTiempoDestino() {

        $subdays = rand(0,10);
        $subhours = rand(0,23);

        $envio = factory(\App\Models\Envio::class)->create([
            'id' => 1,
            'estado_id' => Estado::RECOGIDA,
            'punto_recogida_id' => 2,
            'punto_entrega_id' => 1,
            'usuario_id' => 1,
            'fecha_destino' => \Carbon\Carbon::now()->subDays($subdays)->subHour($subhours),
            'destinatario_id' => 1
        ]);

        $paquete = factory(\App\Models\Paquete::class)->create([
            'id' => 1,
            'peso' => 2,
            'envio_id' => 1
        ]);

        $destinatario = factory(\App\Models\Persona::class)->create([
            'id' => 1,
            'nombre' => 'Destinatariotest',
            'apellidos' => 'Transporter',
            'email' => 'destest@transporter.es',
            'dni' => '11111111H'
        ]);

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/envios-destino')
            ->see($subdays . 'd ' . $subhours . 'h 0m');

    }

}
