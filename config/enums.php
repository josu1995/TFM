<?php

return [

    'mondialRelayEndpoints' => [
        'getPuntos' => 'WSI3_PointRelais_Recherche',
        'getPuntos4' => 'WSI4_PointRelais_Recherche',
        'getTracking' => 'WSI2_TracingColisDetaille',
        'crearEnvio' => 'WSI2_CreationExpedition'
    ],

    'tiposEnvios' => [
        'transporter' => 1,
        'puntoPack' => 2
    ],

    'tiposStores' => [
        'transporter' => 1,
        'puntoPack' => 2
    ],

    'tiposSolicitudRecogida' => [
        'manual' => 1,
        'automatica' => 2
    ]

];
