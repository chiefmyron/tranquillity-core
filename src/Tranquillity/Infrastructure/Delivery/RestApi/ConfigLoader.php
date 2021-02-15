<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use Symfony\Component\Dotenv\Dotenv;
use Tranquillity\Infrastructure\Config\Config;

class ConfigLoader
{
    /**
     * Loads configuration from config files and environment variables
     *
     * @param string $basepath
     * @return Config
     */
    public static function load(string $basepath)
    {
        // Initialise environment variables
        $dotenv = new Dotenv();
        $dotenv->load($basepath . '/.env');

        // Load settings from files
        $config = new Config();
        $config->load($basepath . '/config');
        return $config;
    }
}
