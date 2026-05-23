<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // In production, NEVER use ['*']. Specify your exact React frontend URLs.
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'), 
        'https://enrollment.identity.gov.zm', // Example production domains
        'https://admin.identity.gov.zm'
    ],

    // If using regex for subdomains (optional)
    'allowed_origins_patterns' => [],

    // Specify exactly which headers the React app is allowed to send
    'allowed_headers' => [
        'Content-Type', 
        'X-Requested-With', 
        'Authorization', 
        'Accept',
        'X-XSRF-TOKEN'
    ],

    'exposed_headers' => [],

    // Max age (in seconds) the browser should cache the preflight OPTIONS request
    'max_age' => 86400, // 24 hours

    // Set to true because you are using Sanctum authentication (cookies/tokens)
    'supports_credentials' => true,

];
