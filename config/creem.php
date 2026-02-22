<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Set your CREEM API key and endpoint. You can get these from your
    | CREEM dashboard.
    |
    */

    'api_key' => env('CREEM_API_KEY'),
    'api_url' => env('CREEM_API_URL', 'https://api.creem.io/v1'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Used to verify webhook signatures for security.
    |
    */

    'webhook_secret' => env('CREEM_WEBHOOK_SECRET'),
];