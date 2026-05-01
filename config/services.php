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
   
    'sms' => [
        'username' => env('SMS_API_USERNAME'),
        'password' => env('SMS_API_PASSWORD'),
        'sender_id' => env('SMS_API_SENDER_ID'),
    ],

    'app' => [
        'env' => env('APP_ENV'),
        'url' => env('APP_URL'),
        'ngrok_url' => env('NGROK_URL'),
    ],
    
    'testnumbers' => [
        'number' => env('TEST_NUMBERS'),
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
 
    'zaakpay' => [
        'mode' =>env('ZAAKPAY_MODE'),
        'merchant_identifier' => env('ZAAKPAY_MERCHANT_IDENTIFIER'),
        'secret_key' => env('ZAAKPAY_SECRET_KEY')
    ],

];