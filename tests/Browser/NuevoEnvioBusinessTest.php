<?php

namespace Tests\Browser;

use App\Events\CambiosEstadoBusiness;
use App\Models\DestinatarioBusiness;
use App\Models\DestinoBusiness;
use App\Models\DevolucionBusiness;
use App\Models\EnvioBusiness;
use App\Models\EnvioMondialRelay;
use App\Models\Estado;
use App\Models\OrigenBusiness;
use App\Models\PaqueteBusiness;
use App\Models\PedidoBusiness;
use App\Models\ProductoBusiness;
use App\Models\ProductoEnvioBusiness;
use App\Models\Usuario;
use Tests\Browser\Pages\EnviosPendientesDeExpedicionBusiness;
use Tests\Browser\Pages\NuevoEnvioBusiness;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\CustomDatabaseMigrations;

class NuevoEnvioBusinessTest extends DuskTestCase
{
//    use CustomDatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testPuntoDomicilio()
    {
//        $this->artisan('migrate');

        $this->browse(function (Browser $browser) {
            $resp = $browser->loginAs(Usuario::find(36), 'business')
                    ->visit(new NuevoEnvioBusiness)
                    ->createEnvioPuntoDomicilio()
                    ->assertSee('Envío creado correctamente');

//            $envio = EnvioBusiness::first();
//            \Event::fire(new CambiosEstadoBusiness(array($envio), Estado::find(Estado::PAGADO)));

//            $resp->visit(new EnviosPendientesDeExpedicionBusiness)
//                    ->getEtiqueta();

            // Collect all tabs and grab the last one (recently opened).
//            $window = collect($browser->driver->getWindowHandles())->last();
            // Switch to the new tab that contains the screenshot
//            $resp->driver->switchTo()->window($window);
//
//            $resp->driver->takeScreenshot('Etiqueta-Punto-Domicilio');
        });

        EnvioBusiness::truncate();
        DevolucionBusiness::truncate();
        DestinatarioBusiness::truncate();
        DestinoBusiness::truncate();
        EnvioMondialRelay::truncate();
        OrigenBusiness::truncate();
        PaqueteBusiness::truncate();
        PedidoBusiness::truncate();
        ProductoBusiness::truncate();
        ProductoEnvioBusiness::truncate();
    }

    public function testPuntoPunto()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(Usuario::find(36), 'business')
                ->visit(new NuevoEnvioBusiness)
                ->createEnvioPuntoPunto()
                ->assertSee('Envío creado correctamente');
        });

        EnvioBusiness::truncate();
        DevolucionBusiness::truncate();
        DestinatarioBusiness::truncate();
        DestinoBusiness::truncate();
        EnvioMondialRelay::truncate();
        OrigenBusiness::truncate();
        PaqueteBusiness::truncate();
        PedidoBusiness::truncate();
        ProductoBusiness::truncate();
        ProductoEnvioBusiness::truncate();
    }

    public function testDomicilioPunto()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(Usuario::find(36), 'business')
                ->visit(new NuevoEnvioBusiness)
                ->createEnvioDomicilioPunto()
                ->assertSee('Envío creado correctamente');
        });

        EnvioBusiness::truncate();
        DevolucionBusiness::truncate();
        DestinatarioBusiness::truncate();
        DestinoBusiness::truncate();
        EnvioMondialRelay::truncate();
        OrigenBusiness::truncate();
        PaqueteBusiness::truncate();
        PedidoBusiness::truncate();
        ProductoBusiness::truncate();
        ProductoEnvioBusiness::truncate();
    }

    public function testDomicilioDomicilio()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(Usuario::find(36), 'business')
                ->visit(new NuevoEnvioBusiness)
                ->createEnvioDomicilioDomicilio()
                ->assertSee('Envío creado correctamente');
        });

        EnvioBusiness::truncate();
        DevolucionBusiness::truncate();
        DestinatarioBusiness::truncate();
        DestinoBusiness::truncate();
        EnvioMondialRelay::truncate();
        OrigenBusiness::truncate();
        PaqueteBusiness::truncate();
        PedidoBusiness::truncate();
        ProductoBusiness::truncate();
        ProductoEnvioBusiness::truncate();
    }

}
