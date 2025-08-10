<?php

// config for BinaryHype/Kiroku
return [
    'api' => [
        'url' => env('KIROKU_API_URL', ''),
        'bearer_token' => env('KIROKU_API_BEARER_TOKEN', ''),
    ],
    'queue' => [
        'enabled' => env('KIROKU_QUEUE_ENABLED', false),
    ]
];
