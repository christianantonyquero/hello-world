<?php
// Parse the OAUTH_PROVIDER strin
$oath_provider_str = trim(env('OAUTH_PROVIDERS', ''));
$oauth_providers = [];
if(strlen($oath_provider_str) > 0 ) {
    $oauth_providers = array_map('trim', explode(',', $oath_provider_str));
}
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

    // Shopify
    'shopify' => [
        'store_domain' => env('SHOPIFY_STORE_DOMAIN', ''),
        'storefront_access_token' => env('SHOPIFY_STOREFRONT_ACCESS_TOKEN', '')
    ],

    // Analyitcs
    'analytics' => [
        'gtag_id' => env('GOOGLE_GTAG_ID', ''),
    ],

    // Social Auth
    'oauth' => [
        'providers' => $oauth_providers,
        'login_enabled' => env('OAUTH_LOGIN_ENABLED', false),
    ],
    'valid_oauth_providers' => [
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'google' => 'Google',
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URL'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    // Klaviyo - Email Marketing
    'klaviyo' => [
        'public_key' => env('KLAVIYO_PUBLIC_KEY', ''),
        'private_key' => env('KLAVIYO_PRIVATE_KEY', ''),
        'registration_list_id' => env('KLAVIYO_REGISTRATION_LIST_ID', '')
    ]
];
