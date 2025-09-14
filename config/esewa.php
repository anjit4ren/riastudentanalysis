<?php

return [
    'merchant_id' => env('ESEWA_MERCHANT_ID'),
    'secret_key' => env('ESEWA_SECRET_KEY'),
    'sandbox' => env('ESEWA_SANDBOX', true),
    
    // API endpoints
    'api_url' => env('ESEWA_API_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
    'verify_url' => env('ESEWA_VERIFY_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/transactions'),
    
    // Product code - different for sandbox and production
    'product_code' => env('ESEWA_PRODUCT_CODE', 'EPAYTEST'),
    
    // Route names
    'success_route' => env('ESEWA_SUCCESS_ROUTE', 'esewa.success'),
    'failure_route' => env('ESEWA_FAILURE_ROUTE', 'esewa.failure'),
];