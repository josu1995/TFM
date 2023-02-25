<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class EnviosPendientesDeExpedicionBusiness extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        //return '/business/envios/pendientes-expedicion';
		return '/envios/pendientes-expedicion';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@localizador' => '.localizador-td > a',
        ];
    }

    public function getEtiqueta(Browser $browser) {
        $browser->click('@localizador');
    }
}
