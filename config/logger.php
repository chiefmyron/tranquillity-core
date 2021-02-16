<?php

// PSR standards interfaces
use Psr\Log\LogLevel;

// Logger settings
return [
    'name' => env('LOG_NAME', 'tranquillity-api'),
    'level' => env('LOG_LEVEL', LogLevel::DEBUG),
    'type' => env('LOG_TYPE', 'file-rotating'),
    'options' => [
        'path' => env('LOG_PATH', '../var/logs'),
        'filename' => env('LOG_FILENAME', 'tranquillity-api.log'),
        'maxFiles' => env('LOG_MAX_FILES', 10),
        'outputFormat' => env('LOG_FILE_OUTPUT_FORMAT', '[%datetime%][%context.correlationId%] %channel%.%level_name%: %message% %context% %extra%\n'),
        'dateFormat' => env('LOG_DATE_FORMAT', 'Y-m-d\TH:i:sP')
    ]
];
