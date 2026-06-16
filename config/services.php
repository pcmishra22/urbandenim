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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    'payu' => [
        'merchant_key' => env('PAYU_MERCHANT_KEY'),
        'salt'         => env('PAYU_MERCHANT_SALT'),
        'base_url'     => env('PAYU_ENV') === 'production'
                            ? 'https://secure.payu.in'
                            : 'https://test.payu.in',
    ],

    'cashfree' => [
        'app_id'     => env('CASHFREE_APP_ID'),
        'secret_key' => env('CASHFREE_SECRET_KEY'),
        'env'        => env('CASHFREE_ENV', 'sandbox'), // 'sandbox' or 'production'
        'base_url'   => env('CASHFREE_ENV', 'sandbox') === 'production'
                            ? 'https://api.cashfree.com/pg'
                            : 'https://sandbox.cashfree.com/pg',
    ],

];

