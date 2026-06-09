<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'app' => [
        'env' => env('APP_ENV'),
        'url' => env('APP_URL'),
        'ngrok_url' => env('NGROK_URL'),
        'frontend_url' => env('FRONTEND_URL'),
    ],

    'testnumbers' => [
        'test_numbers' => env('TEST_NUMBERS'),
    ],

    'interakt' => [
        'test_mode' => env('INTERAKT_TEST_MODE', false),
    ],

    'rcs' => [
        'test_mode'=> env('RCS_TEST_MODE', false),
    ],
    
    'razorpay' => [
        'mode' => env('RAZORPAY_MODE'),
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET')
    ],

    'cashfree' => [
        'mode' => env('CASHFREE_MODE'),
        'key' => env('CASHFREE_KEY'),
        'secret' => env('CASHFREE_SECRET')
    ],

    'phonepe' => [
        'mode' => env('PHONEPE_MODE', 'test'),
        'id' => env('PHONEPE_ID'),
        'secret' => env('PHONEPE_SECRET'),
        'version' => env('PHONEPE_CLIENT_VERSION'),
    ],

    'interkt' => [
        'key' => env('INTERAKT_API_KEY'),
        'url' => env('INTERAKT_BASE_URL')
    ],

];
