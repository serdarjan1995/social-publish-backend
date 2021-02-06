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

    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
        'redirect' => env('INSTAGRAM_REDIRECT_URI')
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CONSUMER_ID'),
        'client_secret' => env('TWITTER_CONSUMER_SECRET'),
        'redirect' => env('TWITTER_CALLBACK_URL'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CONSUMER_ID'),
        'client_secret' => env('LINKEDIN_CONSUMER_SECRET'),
        'redirect' => env('LINKEDIN_CALLBACK_URL'),
    ],
    'reddit' => [
        'client_id' => env('REDDIT_APP_ID'),
        'client_secret' => env('REDDIT_APP_SECRET'),
        'redirect' => env('REDDIT_CALLBACK_URL'),
    ],
    'pinterest' => [
        'client_id' => env('PINTEREST_APP_ID'),
        'client_secret' => env('PINTEREST_APP_SECRET'),
        'redirect' => env('PINTEREST_CALLBACK_URL'),
    ],
    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME'),
        'client_id' => null,
        'client_secret' => env('TELEGRAM_TOKEN'),
        'redirect' => env('TELEGRAM_REDIRECT_URI'),
    ],
    'vkontakte' => [
        'client_id' => env('VK_APP_ID'),
        'client_secret' => env('VK_SECURE_KEY'),
        'redirect' => env('VK_CALLBACK_URL')
    ],
    'foursquare' => [
        'client_id' => env('FOURSQUARE_CLIENT_ID'),
        'client_secret' => env('FOURSQUARE_CLIENT_SECRET'),
        'redirect' => env('FOURSQUARE_CALLBACK_URL')
    ],
    'tumblr' => [
        'client_id' => env('TUMBLR_CLIENT_ID'),
        'client_secret' => env('TUMBLR_CLIENT_SECRET'),
        'redirect' => env('TUMBLR_CALLBACK_URL')
    ],


];
