<?php

return [
    'password_algorithm' => env('AUTH_PASSWORD_ALGORITHM', PASSWORD_DEFAULT),
    'password_options' => [
        'cost' => env('AUTH_PASSWORD_COST', 10)
    ],

    // OAuth settings
    'oauth_token_access_lifetime' => env('OAUTH_TOKEN_ACCESS_LIFETIME', 3600),
    'oauth_token_refresh_lifetime' => env('OAUTH_TOKEN_REFRESH_LIFETIME', 30),
    'oauth_auth_code_lifetime' => env('OAUTH_AUTH_CODE_LIFETIME', 600),
    'oauth_private_key_path' => env('OAUTH_PRIVATE_KEY_PATH', '../tranquillity.private.key'),
    'oauth_public_key_path' => env('OAUTH_PUBLIC_KEY_PATH', '../tranquillity.public.key'),
    'oauth_encryption_key' => env('OAUTH_ENCRYPTION_KEY', '1')
];
