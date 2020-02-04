<?php

return [
    'payu_testing' => env('PAYU_TESTING_ENV', true),
    'payu_merchant_id' => env('PAYU_MERCHANT_ID', ''),
    'payu_api_login' => env('PAYU_API_LOGIN', ''),
    'payu_api_key' => env('PAYU_API_KEY', ''),
    'payu_account_id' => env('PAYU_ACCOUNT_ID', ''),
    'payu_country' => env('PAYU_COUNTRY', ''),
    'payu_buyer_email' => env('PAYU_BUYER_EMAIL', ''),
    'payu_payer_name' => env('PAYU_PAYER_NAME', ''),
    'payu_payer_dni' => env('PAYU_PAYER_DNI', ''),
    'pse_redirect_url' => env('PSE_REDIRECT_URL', '')
];
