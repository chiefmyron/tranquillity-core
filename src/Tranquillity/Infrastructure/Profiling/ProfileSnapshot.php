<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling;

use Tranquillity\Infrastructure\Profiling\DataCollector\DataCollectorInterface;

class ProfileSnapshot
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var DataCollectorInterface[]
     */
    private $collectors = [];

    private $ip;

    private $method;

    private $url;

    private $time;

    private $statusCode;

    /**
     * @var ProfileSnapshot
     */
    private $parent;

    /**
     * @var ProfileSnapshot[]
     */
    private $children = [];

    /**
     * Constructor
     *
     * @param string $token Identifier for the profile snapshot
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Set identifier token for the profile snapshot
     *
     * @param string $token Identifier for the profile snapshot
     * @return void
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get identifier token for the profile snapshot
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set parent snapshot for this profile snapshot
     *
     * @param ProfileSnapshot $parent
     * @return void
     */
    public function setParent(ProfileSnapshot $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent snapshot for this profile snapshot
     *
     * @return ProfileSnapshot
     */
    public function getParent(): ProfileSnapshot
    {
        return $this->parent;
    }

    /**
     * Get identifier token for the parent of the profile snapshot
     *
     * @return string|null Identifier for the parent of the profile snapshot
     */
    public function getParentToken()
    {
        if (isset($this->parent)) {
            return $this->parent->getToken();
        }
        return null;
    }

    /**
     * Sets the collection of child snapshots for this profile snapshot
     *
     * @param ProfileSnapshot[] $children
     * @return void
     */
    public function setChildren(array $children)
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * Gets the collection of child snapshots for this profile snapshot
     *
     * @return ProfileSnapshot[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a snapshot as a child of this profile snapshot
     *
     * @param ProfileSnapshot $child
     * @return void
     */
    public function addChild(ProfileSnapshot $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    /**
     * Gets a child snapshot of this profile snapshot identified by the supplied token
     *
     * @param string $token
     * @return ProfileSnapshot|null
     */
    public function getChildByToken(string $token)
    {
        foreach ($this->children as $child) {
            if ($token === $child->getToken()) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Set originating IP address of the request for the profile snapshot
     *
     * @param string|null $ip
     * @return void
     */
    public function setIpAddress(?string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get originating IP address of the request for the profile snapshot
     *
     * @return string|null
     */
    public function getIpAddress()
    {
        return $this->ip;
    }

    /**
     * Set HTTP method of the request for the profile snapshot
     *
     * @param string $method
     * @return void
     */
    public function setHttpMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * Get HTTP method of the request for the profile snapshot
     *
     * @return string|null
     */
    public function getHttpMethod()
    {
        return $this->method;
    }

    /**
     * Set HTTP status code of the response for the profile snapshot
     *
     * @param integer $statusCode
     * @return void
     */
    public function setHttpStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get HTTP status code of the response for the profile snapshot
     *
     * @return int|null
     */
    public function getHttpStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set full URL of the request for the profile snapshot
     *
     * @param string|null $url
     * @return void
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the full URL of the request for the profile snapshot
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set time that the snapshot was taken (as number of seconds from the Unix Epoch)
     * @see https://php.net/manual/en/function.time.php
     *
     * @param integer $time
     * @return void
     */
    public function setTime(int $time)
    {
        $this->time = $time;
    }

    /**
     * Get time that the snapshot was taken (as number of seconds from the Unix Epoch)
     * @see https://php.net/manual/en/function.time.php
     *
     * @return int
     */
    public function getTime()
    {
        if ($this->time === null) {
            return 0;
        }
        return $this->time;
    }

    /**
     * Sets the collection of DataCollectors associated with this profile snapshot
     *
     * @param DataCollectorInterface[] $dataCollectors
     * @return void
     */
    public function setDataCollectors(array $dataCollectors)
    {
        $this->collectors = [];
        foreach ($dataCollectors as $dataCollector) {
            $this->addDataCollector($dataCollector);
        }
    }

    /**
     * Gets the collection of DataCollectors associated with this profile snapshot
     *
     * @return DataCollectorInterface[]
     */
    public function getDataCollectors()
    {
        return $this->collectors;
    }

    /**
     * Add a DataCollector to this profile snapshot
     *
     * @param DataCollectorInterface $dataCollector
     * @return void
     */
    public function addDataCollector(DataCollectorInterface $dataCollector)
    {
        $this->collectors[$dataCollector->getName()] = $dataCollector;
    }

    /**
     * Gets the specified DataCollector associated with this profile snapshot
     *
     * @param string $name
     * @return void
     */
    public function getDataCollector(string $name)
    {
        if (!isset($this->collectors[$name])) {
            throw new \InvalidArgumentException(sprintf('Data collector "%s" does not exist.', $name));
        }

        return $this->collectors[$name];
    }

    /**
     * Checks whether the specified DataCollector has been associated with this profile snapshot
     *
     * @param string $name
     * @return boolean
     */
    public function hasDataCollector(string $name): bool
    {
        return isset($this->collectors[$name]);
    }

    /**
     * Define properties to be populated when unserialised
     *
     * @return array
     */
    public function __sleep()
    {
        return ['token', 'parent', 'children', 'collectors', 'ip', 'method', 'url', 'time', 'statusCode'];
    }

    /**
     * Define properties to be populated when unserialised
     */
    public function __wakeup()
    {
    }
}
