<?php

return [
    'domain' => env('SHOPIFY_DOMAIN'),
    'api_key' => env('SHOPIFY_API_KEY'),
    'api_secret' => env('SHOPIFY_API_SECRET'),
    'access_token' => env('SHOPIFY_ACCESS_TOKEN'),
    'api_version' => env('SHOPIFY_API_VERSION', '2024-01'),
    'scopes' => env('SHOPIFY_SCOPES', 'read_products,write_products'),
];