<?php

declare(strict_types=1);

use Tranquillity\Infrastructure\Delivery\RestApi\Application;

// Initialise the autoloader
$base = realpath(__DIR__ . '/../');
require($base . '/vendor/autoload.php');

// Bootstrap application
$app = Application::bootstrap($base);

// Run app
$app->run();
