<?php

return [
    'hsts' => [
        'enabled' => true,
        'max_age' => 63_072_000,
        'include_subdomains' => true,
        'preload' => true,
    ],

    'x_frame_options' => 'SAMEORIGIN',

    'referrer_policy' => 'strict-origin-when-cross-origin',

    'csp' => (function () {
        $devHosts = [
            'http://localhost:5173',
            'https://localhost:5173',
            'http://127.0.0.1:5173',
            'https://127.0.0.1:5173',
            'http://[::1]:5173',
            'https://[::1]:5173',
        ];

        return [
            'default-src' => [
                "'self'",
            ],
            'script-src' => array_merge([
                "'self'",
                "'unsafe-inline'",
                'https://js.stripe.com',
                'https://*.stripe.com',
                'https://*.deepl.com',
                'https://*.deeplapi.com',
            ], $devHosts),
            'script-src-elem' => array_merge([
                "'self'",
                "'unsafe-inline'",
            ], $devHosts),
            'style-src' => array_merge([
                "'self'",
                "'unsafe-inline'",
                'https://fonts.bunny.net',
            ], $devHosts),
            'font-src' => [
                "'self'",
                'https://fonts.bunny.net',
            ],
            'frame-src' => [
                "'self'",
                'https://js.stripe.com',
                'https://hooks.stripe.com',
                'https://checkout.stripe.com',
            ],
        ];
    })(),
];
