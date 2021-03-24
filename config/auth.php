<?php

return [
    'password_algorithm' => env('AUTH_PASSWORD_ALGORITHM', PASSWORD_DEFAULT),
    'password_options' => [
        'cost' => env('AUTH_PASSWORD_COST', 10)
    ]
];
