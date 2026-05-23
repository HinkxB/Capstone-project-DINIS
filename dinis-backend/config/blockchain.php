<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Blockchain Network Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to your blockchain node (RPC/WSS).
    | Ensure the RPC URL is protected by a firewall or API gateway so 
    | only your Laravel server IP can access the node.
    |
    */

    'node' => [
        'rpc_url'  => env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'),
        'chain_id' => (int) env('BLOCKCHAIN_CHAIN_ID', 1337),
        'timeout'  => env('BLOCKCHAIN_TIMEOUT_SECONDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security & Wallets
    |--------------------------------------------------------------------------
    */

    'security' => [
        // The backend's automated signing key
        'system_private_key' => env('BLOCKCHAIN_SYSTEM_PRIVATE_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Smart Contracts
    |--------------------------------------------------------------------------
    */

    'contracts' => [
        'identity_registry' => env('IDENTITY_CONTRACT_ADDRESS'),
    ],

];
