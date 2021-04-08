<?php

return [
    'password_algorithm' => env('AUTH_PASSWORD_ALGORITHM', PASSWORD_DEFAULT),
    'password_options' => [
        'cost' => env('AUTH_PASSWORD_COST', 10)
    ],
    'oauth_client_allow_credentials_in_body' => env('AUTH_CLIENT_ALLOW_CREDENTIALS_IN_BODY', true),  // Allow client credentials in request body by default
    'oauth_auth_code_lifetime' => env('AUTH_OAUTH_AUTH_CODE_LIFETIME', 30),  // Default to 30 seconds
    'oauth_token_refresh_lifetime' => env('AUTH_OAUTH_TOKEN_REFRESH_LIFETIME', 1209600),  // Default to 14 days
    'oauth_token_refresh_always_issue_new' => env('AUTH_OAUTH_TOKEN_REFRESH_ALWAYS_ISSUE_NEW', true)  // Always issue new refresh tokens by default
];
