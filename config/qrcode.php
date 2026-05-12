<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Generation
    |--------------------------------------------------------------------------
    |
    | Default format for QR code generation.
    | The application will automatically fallback to SVG if PNG is unavailable.
    |
    */
    'default_format' => 'png',
    'fallback_format' => 'svg',
    
    'default' => [
        'size' => 300,
        'margin' => 1,
    ],
];
