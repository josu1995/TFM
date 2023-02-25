<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Estado;

class CrearEnvioTest extends TestCase
{
    use DatabaseTransactions;
    private $user;
    private $envio;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
        Session::start();
        // Definimos envío
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
        $this->envio = [
            'peso' => $peso,
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
            'destino' => $destino,
            '_token' => csrf_token()
        ];
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNuevoEnvioRedirectALogin() {

        $response = $this->call('POST', '/envio/nuevo', $this->envio);
        $response->assertRedirect('/login');
    }

    public function testNuevoEnvioSinCamposDevuelveErrores() {

        $this->actingAs($this->user, 'web')
            ->get('/envio/nuevo');
        $response = $this->call('POST', '/envio/nuevo', ['_token' => csrf_token()]);
        $response->assertRedirect('/envio/nuevo');
        $response->assertSessionHasErrors(['peso', 'alto', 'ancho', 'largo', 'descripcion', 'origen', 'destino', 'nombre', 'apellidos', 'email', 'cobertura', 'embalaje']);
    }

    public function testPesoValidation() {
        $this->checkAttributeValidation('peso', 'paquetes', 'peso');
    }

    public function testAltoValidation() {
        $this->checkAttributeValidation('alto', 'paquetes', 'alto');
    }

    public function testAnchoValidation() {
        $this->checkAttributeValidation('ancho', 'paquetes', 'ancho');
    }

    public function testLargoValidation() {
        $this->checkAttributeValidation('largo', 'paquetes', 'largo');
    }

    public function testEmbalajeValidation() {
        $this->checkAttributeValidation('embalaje', 'envios', 'embalaje_id');
    }

    public function testContenidoValidation() {
        $this->checkAttributeValidation('descripcion', 'envios', 'descripcion');
    }

    public function testCoberturaValidation() {
        $this->checkAttributeValidation('cobertura', 'envios', 'cobertura_id');
    }

    public function testOrigenValidation() {
        $this->checkAttributeValidation('origen', 'envios', 'punto_entrega_id');
    }

    public function testNombreValidation() {
        $this->checkAttributeValidation('nombre', 'personas', 'nombre');
    }

    public function testApellidosValidation() {
        $this->checkAttributeValidation('apellidos', 'personas', 'apellidos');
    }

    public function testEmailValidation() {
        $this->checkAttributeValidation('email', 'personas', 'email');
    }

    // TODO: Movil

    public function testDestinoValidation() {
        $this->checkAttributeValidation('destino', 'envios', 'punto_recogida_id');
    }


    public function testNuevoEnvioConCamposSuccess() {

        $this->actingAs($this->user, 'web')
            ->get('/envio/nuevo');

        // Llamamos a la creacion del envío
        $response = $this->call('POST', '/envio/nuevo', $this->envio);

        // Miramos que redirija al pago del envío
        $response->assertRedirect('/envio/pago');

        // Miramos si existe el envío en BD
        $this->assertDatabaseHas('envios',
            ['embalaje_id' => $this->envio['embalaje'],
                'descripcion' => $this->envio['descripcion'],
                'cobertura_id' => $this->envio['cobertura'],
                'estado_id' => Estado::VALIDADO,
                'punto_entrega_id' => $this->envio['origen'],
                'punto_recogida_id' => $this->envio['destino']
            ]);

        // Miramos si existe el paquete en BD
        $this->assertDatabaseHas('paquetes',
            ['peso' => $this->envio['peso'],
                'alto' => $this->envio['alto'],
                'ancho' => $this->envio['ancho'],
                'largo' => $this->envio['largo']
            ]);

        // Miramos si existe el destinatario en BD
        $this->assertDatabaseHas('personas',
            ['nombre' => $this->envio['nombre'],
                'apellidos' => $this->envio['apellidos'],
                'email' => $this->envio['email']
            ]);

    }

    private function checkAttributeValidation($name, $table, $dbName) {
        $this->checkNoVal($name);
        $this->checkEmptyVal($name);
        $this->checkValWithOnlyBlanks($name);
        $this->checkValWithBlanks($name, $table, $dbName);
    }

    private function checkNoVal($name) {
        $envio = $this->envio;
        unset($envio[$name]);
        $this->actingAs($this->user, 'web')->get('/envio/nuevo');
        $response = $this->call('POST', '/envio/nuevo', $envio);
        $response->assertRedirect('/envio/nuevo');
        $response->assertSessionHasErrors([$name]);
    }

    private function checkEmptyVal($name) {
        $envio = $this->envio;
        $envio[$name] = '';
        $this->actingAs($this->user, 'web')->get('/envio/nuevo');
        $response = $this->call('POST', '/envio/nuevo', $envio);
        $response->assertRedirect('/envio/nuevo');
        $response->assertSessionHasErrors([$name]);
    }

    private function checkValWithOnlyBlanks($name) {
        $envio = $this->envio;
        $envio[$name] = '   ';
        $this->actingAs($this->user, 'web')->get('/envio/nuevo');
        $response = $this->call('POST', '/envio/nuevo', $envio);
        $response->assertRedirect('/envio/nuevo');
        $response->assertSessionHasErrors([$name]);
    }

    private function checkValWithBlanks($name, $table, $dbName) {
        $envio = $this->envio;
        $envio[$name] = '   ' . $this->envio[$name] . '   ';
        $this->actingAs($this->user, 'web')->get('/envio/nuevo');
        $response = $this->call('POST', '/envio/nuevo', $envio);
        $response->assertRedirect('/envio/pago');
        $this->assertDatabaseHas($table, [$dbName => $this->envio[$name]]);
    }


}
