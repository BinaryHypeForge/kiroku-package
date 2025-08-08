<?php

// config for BinaryHype/Kiroku
return [
    'api' => [
        'url' => env('KIROKU_API_URL', ''),
    ],
    'queue' => [
        'enabled' => env('KIROKU_QUEUE_ENABLED', false),
    ]
];
