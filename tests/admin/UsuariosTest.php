<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use Illuminate\Support\Facades\Hash as Hash;

class UsuariosTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    public function testUsuariosListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios');
        $this->assertResponseStatus(200);
    }

    public function testTransportistasListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios-transportistas');
        $this->assertResponseStatus(200);
    }

    public function testTransportistasPotencialesListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios-transportistas-potenciales');
        $this->assertResponseStatus(200);
    }

    public function testClientesListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios-clientes');
        $this->assertResponseStatus(200);
    }

    public function testClientesPotencialesListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios-clientes-potenciales');
        $this->assertResponseStatus(200);
    }

    public function testStoresListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios-puntos');
        $this->assertResponseStatus(200);
    }

    public function testAltaOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuario');
        $this->assertResponseStatus(200);
    }

    public function testAltaMasivaOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios/registro');
        $this->assertResponseStatus(200);
    }

    public function testEliminadosListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuarios/eliminados');
        $this->assertResponseStatus(200);
    }

    public function testCreateNewUser() {

        $newUser = new Usuario();
        $newUser->email = 'newtest@transporter.es';
        $newUser->password = '12345678';
        $newUser->nombre = 'Newtest';
        $newUser->apellidos = 'Transporter';
        $newUser->ciudad = 'Bilbao';
        $newUser->dni = '11111111H';
        $newUser->telefono = '612345678';
        $newUser->fecha_nacimiento = '01/01/1991';


        $this->actingAs($this->user, 'web')
            ->visit('/administracion/usuario')
            ->type($newUser->email, 'email')
            ->type($newUser->password, 'password')
            ->type($newUser->nombre, 'nombre')
            ->type($newUser->apellidos, 'apellidos')
            ->type($newUser->ciudad, 'ciudad')
            ->type($newUser->dni, 'dni')
            ->type($newUser->telefono, 'telefono')
            ->type($newUser->fecha_nacimiento, 'fecha_nacimiento')
            ->press('Guardar');

        $savedUser = Usuario::where('email', $newUser->email)->first();

        $this->seePageIs('/administracion/usuario/' . $savedUser->id);

        $this->seeInDatabase('usuarios',
            ['email' => $newUser->email]);

        $this->assertTrue(Hash::check('12345678', $savedUser->password));

        $this->seeInDatabase('configuraciones',
            ['nombre' => $newUser->nombre,
                'apellidos' => $newUser->apellidos,
                'ciudad' => $newUser->ciudad,
                'telefono' => $newUser->telefono,
                'dni' => $newUser->dni,
                'movil_certificado' => 0,
                'mail_certificado' => 0
            ]);
    }

}
