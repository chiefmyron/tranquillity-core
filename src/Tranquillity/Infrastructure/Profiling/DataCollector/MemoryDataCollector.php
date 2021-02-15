<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MemoryDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $this->updateMemoryUsage();
    }

    /**
     * {@inheritDoc}
     */
    public function lateCollect()
    {
        $this->updateMemoryUsage();
    }

    /**
     * {@inheritDoc}
     */
    public function reset()
    {
        $this->data = [
            'memory' => 0,
            'memory_limit' => $this->convertToBytes(ini_get('memory_limit'))
        ];
    }

    /**
     * Update peak memory usage
     *
     * @return void
     */
    public function updateMemoryUsage()
    {
        $this->data['memory'] = memory_get_peak_usage(true);
    }

    /**
     * Convert reported memory limit as total number of bytes
     *
     * @param string $memoryLimit
     * @return int|float
     */
    private function convertToBytes(string $memoryLimit)
    {
        if ($memoryLimit === '-1') {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max = strtolower(ltrim($memoryLimit, '+'));
        if (0 === strpos($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($memoryLimit, -1)) {
            case 't':
                $max *= 1024;
            // no break
            case 'g':
                $max *= 1024;
            // no break
            case 'm':
                $max *= 1024;
            // no break
            case 'k':
                $max *= 1024;
        }

        return $max;
    }
}
