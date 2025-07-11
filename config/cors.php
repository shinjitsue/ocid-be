<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS')),
    'allowed_origins_patterns' => [],
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', '*')),
    'allowed_headers' => explode(',', env('CORS_ALLOWED_HEADERS', '*')),
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
