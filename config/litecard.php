<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LiteCard Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the LiteCard digital membership card service integration.
    |
    */

    'base_url' => env('LITECARD_BASE_URL', 'https://bff-api.demo.litecard.io'),

    'username' => env('LITECARD_USERNAME'),

    'password' => env('LITECARD_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how long authentication tokens should be cached.
    |
    */

    'token_cache_ttl' => env('LITECARD_TOKEN_CACHE_TTL', 86300), // 24 hours - 5 minutes

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for API timeouts and other settings.
    |
    */

    'timeout' => env('LITECARD_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Enable/disable logging for LiteCard operations.
    |
    */

    'logging' => [
        'enabled' => env('LITECARD_LOGGING_ENABLED', true),
        'channel' => env('LITECARD_LOGGING_CHANNEL', 'stack'),
    ],

];
