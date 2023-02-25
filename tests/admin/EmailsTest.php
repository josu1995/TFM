<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;

class EmailsTest extends TestCase
{
    use DatabaseTransactions;
    private $user;

    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    public function testEmailsListOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/emails');
        $this->assertResponseStatus(200);
    }

    public function testNewEmailOk() {

        $this->actingAs($this->user, 'web')
            ->visit('/administracion/emails/new');
        $this->assertResponseStatus(200);
    }

}
