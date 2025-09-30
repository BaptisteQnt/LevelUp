<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains will receive stateful API
    | authentication cookies. Typically, this should include your local
    | development URL and any first-party SPA URLs used by the frontend.
    |
    */

    'stateful' => array_filter(array_map('trim', explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1%s',
        env('APP_URL')
            ? ','.parse_url(env('APP_URL'), PHP_URL_HOST).(parse_url(env('APP_URL'), PHP_URL_PORT)
                ? ':'.parse_url(env('APP_URL'), PHP_URL_PORT)
                : '')
            : ''
    ))))),

    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    |
    | This option controls which authentication guard Sanctum will utilize
    | while authenticating incoming requests. Typically this should remain
    | set to the "web" guard for first-party SPA authentication.
    |
    */

    'guard' => env('SANCTUM_GUARD', ['web']),

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will
    | be considered expired. If this value is null, personal access tokens
    | will not automatically expire and must be revoked manually.
    |
    */

    'expiration' => env('SANCTUM_EXPIRATION'),

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix personal access tokens when authenticating requests
    | with the provided value. This allows you to differentiate your tokens
    | from other API tokens managed by third-party services.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPAs, Sanctum will need to
    | instantiate certain middleware. You may change the middleware registered
    | under this option as required.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        'ensure_frontend_requests_are_stateful' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'check_credentials' => Laravel\Sanctum\Http\Middleware\CheckCredentials::class,
    ],
];
