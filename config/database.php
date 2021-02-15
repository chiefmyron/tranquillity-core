<?php

return [
    'options' => [
        'auto_generate_proxies' => env('APP_DEV_MODE', false),
        'proxy_dir' => env('DOCTRINE_PROXY_DIR', './var/cache/proxies'),
        'entity_dir' => [
            env('DOCTRINE_ENTITY_DIR', './src/Data/Entities')
        ],
        'mappings_dir' => [
            //env('DOCTRINE_ENTITY_MAPPING_DIR', './src/Tranquillity/Infrastructure/Persistence/Doctrine/Mapping') => 'Tranquillity\Domain\Model'
            env('DOCTRINE_ENTITY_MAPPING_DIR', './src/Tranquillity/Infrastructure/Persistence/Doctrine/Mapping')
        ],
        'cache' => null,
        'table_prefix' => env('DB_TABLE_PREFIX', '')
    ],
    'connection' => [
        'driver' => 'pdo_mysql',
        'host' => env('DB_HOSTNAME', 'localhost'),
        'dbname' => env('DB_DATABASE', 'tranquility'),
        'user' => env('DB_USERNAME', 'tranquility'),
        'password' => env('DB_PASSWORD', 'secret'),
        'port' => env('DB_PORT', 3306)
    ]
];
