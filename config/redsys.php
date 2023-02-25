<?php

return [
    'defaults' => [
        'merchant_key' => env('REDSYS_MERCHANT_KEY'),
        'merchant_code' => env('REDSYS_MERCHANT_CODE'),
        'transaction_type' => env('REDSYS_TRANSACTION_TYPE'),
        'consumer_language' => env('REDSYS_CONSUMER_LANGUAGE'),
        'terminal' => env('REDSYS_TERMINAL'),
        'currency' => env('REDSYS_CURRENCY'),
        'url' => env('REDSYS_URL'),
        'merchant_data' => env('REDSYS_MERCHANT_DATA'),
        'test_mode' => env('REDSYS_TEST_MODE'),
    ]
];
