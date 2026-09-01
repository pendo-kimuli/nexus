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
        'africastalking' => [
        'username' => env('AT_USERNAME'),
        'api_key' => env('AT_API_KEY'),
    ],

    'daraja' => [
        'consumer_key' => env('DARAJA_CONSUMER_KEY'),
        'consumer_secret' => env('DARAJA_CONSUMER_SECRET'),
        'shortcode' => env('DARAJA_SHORTCODE'),
        'initiator_name' => env('DARAJA_INITIATOR_NAME'),
        'security_credential' => env('DARAJA_SECURITY_CREDENTIAL'),
        'timeout_url' => env('DARAJA_TIMEOUT_URL'),
        'result_url' => env('DARAJA_RESULT_URL'),
    ],

];
