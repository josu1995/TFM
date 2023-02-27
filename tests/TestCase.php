<?php

namespace Tests;

use App\Models\AjustesDevolucionBusiness;
use App\Models\BusinessRegistroMarketplaces;
use App\Models\BusinessRegistroTiendaOnline;
use App\Models\BusinessRegistroTipoNegocio;
use App\Models\Configuracion;
use App\Models\ConfiguracionBusiness;
use App\Models\OpcionCosteDevolucionBusiness;
use App\Models\OpcionEtiquetaDevolucionBusiness;
use App\Models\Rol;
use App\Models\TiposRecogidaBusiness;
use App\Models\Usuario;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan as Artisan;
use Uuid;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://192.168.0.14';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.testing');

        $app->make(Kernel::class)->bootstrap();

        Artisan::call('cache:clear');

        return $app;
    }

    public function createBusinessUser()
    {
        $usuario = factory(Usuario::class)->create([
            'id' => 1,
            'identificador' => Uuid::generate()->string,
            'password' => Crypt::encrypt('123'),
            'email' => 'test@transporter.es',
        ]);

        $usuario->configuracion()->save(factory(Configuracion::class)->make([
            'id' => 1,
            'nombre' => 'Test',
            'apellidos' => 'Transporter',
            'ciudad' => 'Bilbao',
            'telefono' => '612345678',
            'movil_certificado' => 1,
            'mail_certificado' => 1,
        ]));

        $usuario->configuracionBusiness()->save(factory(ConfiguracionBusiness::class)->make([
            'id' => 1,
            'tarifa_id' => 1,
            'razon_social' => 'Test Transporter SL',
            'direccion' => 'Avenida Altos Hornos de Vizcaya 33',
            'nif' => '11111111H',
            'codigo_postal' => '48902',
            'ciudad' => 'Barakaldo',
            'nombre_comercial' => 'Test Online',
            'web' => 'testransporter.com',
            'tipo_negocio_id' => BusinessRegistroTipoNegocio::TECNOLOGIA_ELECTRONICA,
            'tienda_online_id' => BusinessRegistroTiendaOnline::PRESTASHOP,
            'marketplaces_id' => BusinessRegistroMarketplaces::NO_MARKETPLACES,
            'api_key' => 'testApiKey'
        ]));

        $usuario->configuracionBusiness->tiposRecogida()->attach(TiposRecogidaBusiness::DOMICILIO);
        $usuario->configuracionBusiness->tiposRecogida()->attach(TiposRecogidaBusiness::STORE);

        $usuario->configuracionBusiness->ajustesDevolucion()->save(factory(AjustesDevolucionBusiness::class)->make([
            'plazo' => 14,
            'color' => '#ee8026',
            'opcion_etiqueta_id' => OpcionEtiquetaDevolucionBusiness::PREIMPRESA,
            'opcion_store' => 1,
            'opcion_domicilio' => 0,
            'opcion_coste_id' => OpcionCosteDevolucionBusiness::PREPAGADO
        ]));

        $usuario->roles()->attach(Rol::USUARIO);
        $usuario->roles()->attach(Rol::BUSINESS);

        return $usuario;
    }
}
