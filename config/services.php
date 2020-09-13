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

    'flickr' => [
        'client_id' => env('FLICKR_KEY'),
        'client_secret' => env('FLICKR_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/flickr/callback'
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/oauth/google/callback'
    ],

    'slack' => [
        'webhook_url' => env('SLACK_HOOK')
    ],

    'httpclient' => [
        'debug' => false,
        'decode_content' => true,
        'delay' => 1000
    ],

    'authenticated' => [
        'emails' => explode(',', env('AUTHENTICATED_EMAILS', 'soulevilx@gmail.com'))
    ]
];
