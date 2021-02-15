<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\Storage;

use Tranquillity\Infrastructure\Profiling\ProfileSnapshot;

interface ProfilerStorageInterface
{

    /**
     * Finds profiler tokens for the given criteria.
     *
     * @param array $criteria Accepted keys are: 'ip', 'url', 'limit', 'method', 'statusCode', 'startTime', 'endTime'
     * @return array
     */
    public function find(array $criteria = []);

    /**
     * Reads data associated with the given token.
     *
     * The method returns false if the token does not exist in the storage.
     *
     * @return ProfileSnapshot|null The profile associated with token
     */
    public function read(string $token): ?ProfileSnapshot;

    /**
     * Saves a ProfileSnapshot.
     *
     * @return bool Write operation successful
     */
    public function write(ProfileSnapshot $profile): bool;

    /**
     * Purges all data from the database.
     *
     * @return void
     */
    public function purge();
}
