<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class EnvironmentDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'environment';
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $token = '';
        $headerTokenValues = $response->getHeader('X-Debug-Token');
        if (count($headerTokenValues) > 0) {
            $token = $headerTokenValues[0];
        }

        $this->data = [
            'token' => $token,
            'env' => isset($this->kernel) ? $this->kernel->getEnvironment() : 'n/a',
            'debug' => isset($this->kernel) ? $this->kernel->isDebug() : 'n/a',
            'php_version' => PHP_VERSION,
            'php_architecture' => PHP_INT_SIZE * 8,
            'php_intl_locale' => class_exists('Locale', false) && \Locale::getDefault() ? \Locale::getDefault() : 'n/a',
            'php_timezone' => date_default_timezone_get(),
            'xdebug_enabled' => \extension_loaded('xdebug'),
            'apcu_enabled' => \extension_loaded('apcu') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN),
            'zend_opcache_enabled' => \extension_loaded('Zend OPcache') && filter_var(ini_get('opcache.enable'), FILTER_VALIDATE_BOOLEAN),
            'sapi_name' => PHP_SAPI
        ];

        if (preg_match('~^(\d+(?:\.\d+)*)(.+)?$~', $this->data['php_version'], $matches) && isset($matches[2])) {
            $this->data['php_version'] = $matches[1];
            $this->data['php_version_extra'] = $matches[2];
        }
    }

    /**
     * @inheritdoc
     */
    public function lateCollect()
    {
        // Transform collected data into an array that can be serialised
        $this->data = $this->cloneVar($this->data);
    }
}
