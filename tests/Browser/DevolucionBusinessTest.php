<?php

namespace Tests\Browser;

use App\Events\CambiosEstadoBusiness;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Models\Usuario;
use Tests\Browser\Pages\NuevoEnvioBusiness;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DevolucionBusinessTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testDomicilioDomicilio()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(Usuario::find(36), 'business')
                ->visit(new NuevoEnvioBusiness)
                ->createEnvioDomicilioDomicilio();

            $envio = EnvioBusiness::first();
            \Event::fire(new CambiosEstadoBusiness(array($envio), Estado::find(Estado::PAGADO)));



        });
    }
}
