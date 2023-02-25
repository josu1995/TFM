<?php

namespace Tests\api\tbusiness;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\utils\BusinessApiHelper;

class EnvioTest extends BusinessApiHelper
{
    use DatabaseTransactions;

    public function testPostEnvioDomicilioSuccess()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(200);

        $this->checkEnvioInDatabase($requestJson, $response);
    }

    public function testPostEnvioStoreSuccess()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
                'storeId' => '2014613'
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(200);

        $this->checkEnvioInDatabase($requestJson, $response);
    }

    public function testPostEnvioWithoutOrderReferenceSuccess()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(200);

        $this->checkEnvioInDatabase($requestJson, $response);
    }

    public function testPostEnvioWithoutProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutPackageError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutCustomerError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutDestinationError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithBadProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutPackageWidthError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutCustomerEmailError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutDestinationParamsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithoutParamsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), []);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithRepeatedOrderReferenceError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithNoWeightProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithNoQuantityProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 0, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 0, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioWithOverWeightProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 20],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 20]
            ],
            'package' => [
                'height' => 22,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioDomicilioWithOverHeightProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 602,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioStoreWithOverHeightProductsError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 602,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
                'storeId' => '2014613'
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }

    public function testPostEnvioStoreWithRandomStoreIdError()
    {
        $this->addPreferenciaRecogidaToUser();

        $requestJson = [
            'orderReference' => 'TestRef',
            'products' => [
                ['name' => 'Camiseta', 'quantity' => 1, 'weight' => 0.24],
                ['name' => 'Pantalón', 'quantity' => 1, 'weight' => 0.54]
            ],
            'package' => [
                'height' => 20,
                'width' => 19,
                'depth' => 20
            ],
            'customer' => [
                'firstName' => 'Test',
                'lastName' => 'Customer',
                'email' => 'test@transporter.es',
                'phone' => '612345678',
            ],
            'destination' => [
                'country' => 'ES',
                'postcode' => '28005',
                'address1' => 'Gran via 22',
                'address2' => '6 derecha',
                'storeId' => '2999999'
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->apiKey,
        ])->json('POST', route('api_business_post_shipment'), $requestJson);

        $response->assertStatus(400);
    }
}
