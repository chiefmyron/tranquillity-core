<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Profiling\DataCollector\DataCollectorInterface;
use Tranquillity\Infrastructure\Profiling\DataCollector\LateDataCollectorInterface;
use Tranquillity\Infrastructure\Profiling\Storage\ProfilerStorageInterface;

class Profiler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfilerStorageInterface
     */
    private $storage;

    /**
     * @var DataCollectorInterface[]
     */
    private $dataCollectors = [];

    /**
     * @var boolean
     */
    private $enabled = false;

    /**
     * Constructor
     *
     * @param ProfilerStorageInterface $storage
     * @param LoggerInterface $logger
     * @param boolean $enabled
     */
    public function __construct(ProfilerStorageInterface $storage, LoggerInterface $logger = null, bool $enabled = false)
    {
        $this->storage = $storage;
        $this->logger = $logger;
        $this->enabled = $enabled;
    }

    /**
     * Disable the profiler
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Enable the profiler
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Loads the ProfileSnapshot for a given Response
     *
     * @param ResponseInterface $response
     * @return ProfileSnapshot|null
     */
    public function loadProfileFromResponse(ResponseInterface $response)
    {
        if (!$token = $response->headers->get('X-Debug-Token')) {
            return null;
        }

        return $this->loadProfile($token);
    }

    /**
     * Loads the ProfileSnapshot for a given token
     *
     * @param string $token
     * @return ProfileSnapshot|null
     */
    public function loadProfile(string $token)
    {
        return $this->storage->read($token);
    }

    /**
     * Writes a ProfileSnapshot to storage. This is the point when any last-minute data
     * collection takes place.
     *
     * @param ProfileSnapshot $profileSnapshot
     * @return bool
     */
    public function saveProfile(ProfileSnapshot $profileSnapshot)
    {
        // Trigger late data collection for all collectors in the profile entry
        foreach ($profileSnapshot->getDataCollectors() as $collector) {
            if ($collector instanceof LateDataCollectorInterface) {
                $collector->lateCollect();
            }
        }

        // Write profile to storage
        $result = $this->storage->write($profileSnapshot);
        if ($result !== true && $this->logger !== null) {
            $this->logger->warning('Unable to store the profiler entry.', ['configured_storage' => \get_class($this->storage)]);
        }

        return $result;
    }

    /**
     * Purges all data from storage
     *
     * @return void
     */
    public function purge()
    {
        $this->storage->purge();
    }

    /**
     * Find profiler tokens for the given criteria
     *
     * @param array $criteria
     * @return array An array of ProfileSnapshot tokens
     *
     * @see https://php.net/datetime.formats for the supported date/time formats
     */
    public function find(array $criteria = [])
    {
        return $this->storage->find($criteria);
    }

    /**
     * Collects data for the given Response.
     *
     * @return ProfileSnapshot|null A ProfileSnapshot instance or null if the profiler is disabled
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, \Throwable $exception = null)
    {
        if (false === $this->enabled) {
            return null;
        }

        $profileSnapshot = new ProfileSnapshot(substr(hash('sha256', uniqid('' . mt_rand(), true)), 0, 6));
        $profileSnapshot->setTime(time());
        $profileSnapshot->setUrl((string)$request->getUri());
        $profileSnapshot->setHttpMethod($request->getMethod());
        $profileSnapshot->setHttpStatusCode($response->getStatusCode());

        // Get IP address from request
        $ip = $request->getAttribute('ip_address', null);
        if (is_null($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }
        if (is_null($ip)) {
            $ip = 'Unknown';
        }
        $profileSnapshot->setIpAddress($ip);

        $headerTokenValues = $response->getHeader('X-Debug-Token');
        if (count($headerTokenValues) > 0) {
            $response = $response->withAddedHeader('X-Previous-Debug-Token', $headerTokenValues[0]);
        }
        $response = $response->withAddedHeader('X-Debug-Token', $profileSnapshot->getToken());

        foreach ($this->getDataCollectors() as $collector) {
            $collector->collect($request, $response, $exception);

            // we need to clone for sub-requests
            $profileSnapshot->addDataCollector(clone $collector);
        }

        return $profileSnapshot;
    }

    /**
     * Resets data in all collectors
     *
     * @return void
     */
    public function reset()
    {
        foreach ($this->getDataCollectors() as $collector) {
            $collector->reset();
        }
        $this->enabled = $this->initiallyEnabled;
    }

    /**
     * Gets all of the DataCollectors associated with the profiler
     *
     * @return DataCollectorInterface[]
     */
    public function getDataCollectors()
    {
        return $this->dataCollectors;
    }

    /**
     * Sets and overwrites all of the DataCollectors associated with the profiler
     *
     * @param DataCollectorInterface[] $collectors An array of collectors
     * @return void
     */
    public function setDataCollectors(array $collectors = [])
    {
        $this->dataCollectors = [];
        foreach ($collectors as $collector) {
            $this->addDataCollector($collector);
        }
    }

    /**
     * Add a DataCollector to the profiler
     *
     * @param DataCollectorInterface $collector
     * @return void
     */
    public function addDataCollector(DataCollectorInterface $collector)
    {
        $this->dataCollectors[$collector->getName()] = $collector;
    }

    /**
     * Returns true if a DataCollector for the given name exists
     *
     * @param string $name A collector name
     * @return bool
     */
    public function hasDataCollector(string $name): bool
    {
        return isset($this->dataCollectors[$name]);
    }

    /**
     * Gets a DataCollector by name
     *
     * @param string $name A collector name
     * @return DataCollectorInterface A DataCollectorInterface instance
     * @throws \InvalidArgumentException if the collector does not exist
     */
    public function getDataCollector(string $name): DataCollectorInterface
    {
        if (!isset($this->dataCollectors[$name])) {
            throw new \InvalidArgumentException(sprintf('Collector "%s" does not exist.', $name));
        }

        return $this->dataCollectors[$name];
    }

    private function getTimestamp(?string $value): ?int
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            $value = new \DateTime(is_numeric($value) ? '@' . $value : $value);
        } catch (\Exception $e) {
            return null;
        }

        return $value->getTimestamp();
    }
}
