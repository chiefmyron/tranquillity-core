<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

interface LateDataCollectorInterface extends DataCollectorInterface
{
    /**
     * Performs very last-minute data collection
     *
     * @return void
     */
    public function lateCollect();
}
