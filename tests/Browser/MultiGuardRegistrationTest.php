<?php

namespace Tests\Browser;

use App\Models\Usuario;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class MultiGuardRegistrationTest extends DuskTestCase
{

    /**
     * A Dusk test example.
     *
     * @return void
     */
//    public function testRegisterWebAndDriver()
//    {
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/registro')
//                ->waitFor('#registro_nombre')
//                ->type('#registro_nombre', 'Test')
//                ->type('#registro_ciudad', 'Bilbao')
//                ->type('#registro_email', 'test@transporter.es')
//                ->type('#registro_password', '123456')
//                ->press('Registrarse')
//                ->waitForLocation('/inicio')
//                ->assertPathIs('/inicio');
//
//            $browser->visit('/drivers')
//                ->click('.headerButton > li > a.btn')
//                ->waitFor('#nombre')
//                ->type('#nombre', 'Test')
//                ->type('#ciudad', 'Bilbao')
//                ->type('#email', 'test@transporter.es')
//                ->type('#password', '123456')
//                ->click('.btn-submit-particular')
//                ->waitForLocation('/vinculacion')
//                ->assertPathIs('/vinculacion')
//                ->waitFor('#password')
//                ->type('#password', '123456')
//                ->click('.btn-vincular')
//                ->waitForLocation('/drivers/inicio')
//                ->assertPathIs('/drivers/inicio');
//
//            $user = Usuario::where('email', 'test@transporter.es')->first();
//            $user->configuracion()->forceDelete();
//            $user->forceDelete();
//        });
//
//    }
//
//    public function testRegisterDriverAndWeb()
//    {
//        $this->browse(function (Browser $browser) {
//
//            $browser->visit('/drivers')
//                ->click('.headerButton > li > a.btn')
//                ->waitFor('#nombre')
//                ->type('#nombre', 'Test')
//                ->type('#ciudad', 'Bilbao')
//                ->type('#email', 'test@transporter.es')
//                ->type('#password', '123456')
//                ->click('.btn-submit-particular')
//                ->waitForLocation('/drivers/inicio')
//                ->assertPathIs('/drivers/inicio');
//
//            $browser->visit('/registro')
//                ->waitFor('#registro_nombre')
//                ->type('#registro_nombre', 'Test')
//                ->type('#registro_ciudad', 'Bilbao')
//                ->type('#registro_email', 'test@transporter.es')
//                ->type('#registro_password', '123456')
//                ->press('Registrarse')
//                ->waitForLocation('/vinculacion')
//                ->assertPathIs('/vinculacion')
//                ->waitFor('#password')
//                ->type('#password', '123456')
//                ->click('.btn-vincular')
//                ->waitForLocation('/inicio')
//                ->assertPathIs('/inicio');
//
//            $user = Usuario::where('email', 'test@transporter.es')->first();
//            $user->configuracion()->forceDelete();
//            $user->forceDelete();
//        });
//
//    }

}
