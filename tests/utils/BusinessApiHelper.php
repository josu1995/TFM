<?php

namespace Tests\utils;

use App\Models\CodigoPostal;
use App\Models\Estado;
use App\Models\Pais;
use App\Models\PreferenciaRecogidaBusiness;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use Tests\TestCase;

class BusinessApiHelper extends TestCase
{
    protected $user;
    protected $apiKey;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->createBusinessUser();
        $this->apiKey = $this->user->configuracionBusiness->api_key;
    }

    public function addPreferenciaRecogidaToUser()
    {
        $this->user->configuracionBusiness->preferenciaRecogida()->save(
            factory(PreferenciaRecogidaBusiness::class)->make([
                'tipo_recogida_id' => TiposRecogidaBusiness::DOMICILIO,
                'cp_id' => CodigoPostal::where([['codigo_postal', '48902'], ['codigo_pais', 'ES']])->first()->id,
                'direccion' => 'Avenida Altos Hornos de Vizcaya 33',
                'tipo_solicitud_recogida' => 2,
                'dias' => 'L,M',
                'franja_horaria_id' => 1
            ])
        );
    }

    public function checkEnvioInDatabase($requestJson, $response)
    {
        $this->assertDatabaseHas('envios_business', [
            'id' => (int) $response->getContent()
        ]);

        $this->assertDatabaseHas('envios_business', [
            'id' => (int) $response->getContent(),
            'tipo_origen_id' => TipoOrigenBusiness::PREFERENCIA,
            'origen_id' => $this->user->configuracionBusiness->preferenciaRecogida->id,
            'estado_id' => Estado::VALIDADO,
        ]);

        if (isset($requestJson['orderReference'])) {
            $this->assertDatabaseHas('pedidos_business', [
                'num_pedido' => $requestJson['orderReference'],
            ]);
        }

        $this->assertDatabaseHas('paquetes_business', [
            'alto' => $requestJson['package']['height'],
            'ancho' => $requestJson['package']['width'],
            'largo' => $requestJson['package']['depth']
        ]);

        $this->assertDatabaseHas('destinatarios_business', [
            'nombre' => $requestJson['customer']['firstName'],
            'apellidos' => $requestJson['customer']['lastName'],
            'email' => $requestJson['customer']['email'],
            'telefono' => $requestJson['customer']['phone']
        ]);

        if (!isset($requestJson['destination']['storeId']) || !$requestJson['destination']['storeId']) {
            $destinoConstraints = [
                'pais_id' => Pais::where('iso2', $requestJson['destination']['country'])->first()->id,
                'cp_id' => CodigoPostal::where([
                    ['codigo_postal', $requestJson['destination']['postcode']],
                    ['codigo_pais', $requestJson['destination']['country']]
                ])->first()->id,
                'tipo_entrega_id' => TiposRecogidaBusiness::DOMICILIO,
                'direccion' => $requestJson['destination']['address1'] . ' ' . $requestJson['destination']['address2']
            ];
        } else {
            $destinoConstraints = [
                'pais_id' => Pais::where('iso2', $requestJson['destination']['country'])->first()->id,
                'cp_id' => CodigoPostal::where([
                    ['codigo_postal', $requestJson['destination']['postcode']],
                    ['codigo_pais', $requestJson['destination']['country']]
                ])->first()->id,
                'tipo_entrega_id' => TiposRecogidaBusiness::STORE,
                'direccion' => $requestJson['destination']['address1'],
                'store_id' => substr($requestJson['destination']['storeId'], 1)
            ];
        }

        $this->assertDatabaseHas('destinos_business', $destinoConstraints);

        foreach ($requestJson['products'] as $product) {
            $this->assertDatabaseHas('productos_business', [
                'nombre' => $product['name'],
                'peso' => $product['weight']
            ]);
        }
    }
}
