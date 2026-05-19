<?php

return [
    'fawry' => [
        'merchant_code' => env('FAWRY_MERCHANT_CODE'),
        'secure_key' => env('FAWRY_SECURE_KEY'),
        'charge_url' => env('FAWRY_CHARGE_URL', 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/charge'),
        'status_url' => env('FAWRY_STATUS_URL', 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/status/v2'),
        'currency' => env('FAWRY_CURRENCY', 'EGP'),
        'language' => env('FAWRY_LANGUAGE', 'en-gb'),
        'verify_signature' => env('FAWRY_VERIFY_SIGNATURE', true),
        'payment_methods' => [
            'fawry_card' => 'PayUsingCC',
            'fawry_wallet' => 'MWALLET',
            'instapay' => 'PayUsingCC',
            'apple_pay' => 'APPLEPAY',
        ],
    ],
];
