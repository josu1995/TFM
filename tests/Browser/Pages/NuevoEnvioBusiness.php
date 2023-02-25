<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use function PHPSTORM_META\type;

class NuevoEnvioBusiness extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        //return '/business/nuevo-envio/crear';
		return '/nuevo-envio/crear';
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
            '@pedidoId' => 'input[name="referencia_pedido"]',
            '@nombreProducto' => 'input[name^="nombre_producto"]',
            '@numProducto' => 'input[name^="num_productos"]',
            '@pesoProducto' => 'input[name^="peso_producto"]',
            '@embalaje' => '#embalaje',
            '@embalajeAlto' => 'input[name="alto"]',
            '@embalajeAncho' => 'input[name="ancho"]',
            '@embalajeLargo' => 'input[name="largo"]',
            '@origenCP' => '#cp-origen-autocomplete',
            '@origenCPAutocomplete' => '#cp-origen-autocomplete + .business-autocomplete',
            '@origenTipo' => '#tipo_recogida_origen',
            '@origenStore' => 'input[name="store_origen"]',
            '@origenDireccion' => 'input[name="domicilio_origen"]',
            '@destinatarioNombre' => 'input[name="nombre"]',
            '@destinatarioApellidos' => 'input[name="apellidos"]',
            '@destinatarioEmail' => 'input[name="email"]',
            '@destinatarioTelefono' => 'input[name="telefono"]',
            '@destinoPais' => 'input[name="pais_destino"]',
            '@destinoPaisAutocomplete' => 'input[name="pais_destino"] + .business-autocomplete',
            '@destinoCP' => '#cp-destino-autocomplete',
            '@destinoCPAutocomplete' => '#cp-destino-autocomplete + .business-autocomplete',
            '@destinoTipo' => '#tipo_entrega_destino',
            '@destinoStore' => 'input[name="store_destino"]',
            '@destinoDireccion' => 'input[name="direccion_destino"]',
            '@crearButton' => 'button[type="submit"]',
            '@seleccionarStoreButton' => '.seleccionar-store-btn:first'
        ];
    }

    public function createEnvioPuntoDomicilio(Browser $browser)
    {
        $browser->resize(1920, 1080)
            ->type('@nombreProducto', 'Camiseta Volcom blanca')
            ->type('@numProducto', 1)
            ->type('@pesoProducto', 0.25)
            ->select('@embalaje', 0)
            ->type('@embalajeAlto', 10)
            ->type('@embalajeAncho', 20)
            ->type('@embalajeLargo', 10)
            ->type('@destinatarioNombre', 'Antonio')
            ->type('@destinatarioApellidos', 'Gutierrez')
            ->type('@destinatarioEmail', 'test@transporter.es')
            ->type('@destinatarioTelefono', '618032874')
            ->type('@destinoPais', 'España')
            ->waitFor('@destinoPaisAutocomplete')
            ->click('@destinoPaisAutocomplete')
            ->type('@destinoCP', '28003')
            ->waitFor('@destinoCPAutocomplete')
            ->click('@destinoCPAutocomplete')
            ->select('@destinoTipo', 1)
            ->type('@destinoDireccion', 'Gran Via 25 6D')
            ->screenshot('Envio-Punto-Domicilio')
            ->click('@crearButton');
    }

    public function createEnvioPuntoPunto(Browser $browser)
    {
        $browser->resize(1920, 1080)
            ->type('@nombreProducto', 'Camiseta Volcom blanca')
            ->type('@numProducto', 1)
            ->type('@pesoProducto', 0.25)
            ->select('@embalaje', 0)
            ->type('@embalajeAlto', 10)
            ->type('@embalajeAncho', 20)
            ->type('@embalajeLargo', 10)
            ->click('#cambiar-origen-link')
            ->type('@origenCP', '48901')
            ->waitFor('@origenCPAutocomplete')
            ->click('@origenCPAutocomplete')
            ->select('@origenTipo', 2)
            ->waitFor('#stores-list')
            ->click('#stores-list .seleccionar-store-btn')
            ->type('@destinatarioNombre', 'Antonio')
            ->type('@destinatarioApellidos', 'Gutierrez')
            ->type('@destinatarioEmail', 'test@transporter.es')
            ->type('@destinatarioTelefono', '618032874')
            ->type('@destinoPais', 'España')
            ->waitFor('@destinoPaisAutocomplete')
            ->click('@destinoPaisAutocomplete')
            ->type('@destinoCP', '28003')
            ->waitFor('@destinoCPAutocomplete')
            ->click('@destinoCPAutocomplete')
            ->select('@destinoTipo', 2)
            ->waitFor('#stores-list')
            ->click('#stores-list .seleccionar-store-btn')
            ->waitUntilMissing('#stores-list')
            ->screenshot('Envio-Punto-Punto')
            ->click('@crearButton');
    }

    public function createEnvioDomicilioPunto(Browser $browser)
    {
        $browser->resize(1920, 1080)
            ->type('@nombreProducto', 'Camiseta Volcom blanca')
            ->type('@numProducto', 1)
            ->type('@pesoProducto', 0.25)
            ->select('@embalaje', 0)
            ->type('@embalajeAlto', 10)
            ->type('@embalajeAncho', 20)
            ->type('@embalajeLargo', 10)
            ->click('#cambiar-origen-link')
            ->type('@origenCP', '48901')
            ->waitFor('@origenCPAutocomplete')
            ->click('@origenCPAutocomplete')
            ->select('@origenTipo', 1)
            ->type('@origenDireccion', 'Avenida Altos Hornos de Vizcaya 33')
            ->type('@destinatarioNombre', 'Antonio')
            ->type('@destinatarioApellidos', 'Gutierrez')
            ->type('@destinatarioEmail', 'test@transporter.es')
            ->type('@destinatarioTelefono', '618032874')
            ->type('@destinoPais', 'España')
            ->waitFor('@destinoPaisAutocomplete')
            ->click('@destinoPaisAutocomplete')
            ->type('@destinoCP', '28003')
            ->waitFor('@destinoCPAutocomplete')
            ->click('@destinoCPAutocomplete')
            ->select('@destinoTipo', 2)
            ->waitFor('#stores-list')
            ->click('#stores-list .seleccionar-store-btn')
            ->waitUntilMissing('#stores-list')
            ->screenshot('Envio-Domicilio-Punto')
            ->click('@crearButton');
    }


    public function createEnvioDomicilioDomicilio(Browser $browser)
    {
        $browser->resize(1920, 1080)
            ->type('@nombreProducto', 'Camiseta Volcom blanca')
            ->type('@numProducto', 1)
            ->type('@pesoProducto', 0.25)
            ->select('@embalaje', 0)
            ->type('@embalajeAlto', 10)
            ->type('@embalajeAncho', 20)
            ->type('@embalajeLargo', 10)
            ->click('#cambiar-origen-link')
            ->type('@origenCP', '48901')
            ->waitFor('@origenCPAutocomplete')
            ->click('@origenCPAutocomplete')
            ->select('@origenTipo', 1)
            ->type('@origenDireccion', 'Avenida Altos Hornos de Vizcaya 33')
            ->type('@destinatarioNombre', 'Antonio')
            ->type('@destinatarioApellidos', 'Gutierrez')
            ->type('@destinatarioEmail', 'test@transporter.es')
            ->type('@destinatarioTelefono', '618032874')
            ->type('@destinoPais', 'España')
            ->waitFor('@destinoPaisAutocomplete')
            ->click('@destinoPaisAutocomplete')
            ->type('@destinoCP', '28003')
            ->waitFor('@destinoCPAutocomplete')
            ->click('@destinoCPAutocomplete')
            ->select('@destinoTipo', 1)
            ->type('@destinoDireccion', 'Gran Via 25 6D')
            ->screenshot('Envio-Domicilio-Domicilio')
            ->click('@crearButton');
    }
}
