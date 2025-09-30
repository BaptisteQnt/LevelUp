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

    'csp' => [
        'default-src' => [
            "'self'",
        ],
        'script-src' => [
            "'self'",
            'https://js.stripe.com',
            'https://*.stripe.com',
            'https://*.deepl.com',
            'https://*.deeplapi.com',
            'http://localhost:5173',
            'https://localhost:5173',
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'",
            'http://localhost:5173',
            'https://localhost:5173',
        ],
        'frame-src' => [
            "'self'",
            'https://js.stripe.com',
            'https://hooks.stripe.com',
            'https://checkout.stripe.com',
        ],
    ],
];
