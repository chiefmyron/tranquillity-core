<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\Storage;

use FilesystemIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Tranquillity\Infrastructure\Profiling\ProfileSnapshot;

class FileProfilerStorage implements ProfilerStorageInterface
{
    /**
     * Folder where the profile snapshots are stored
     *
     * @var string
     */
    private $path;

    /**
     * Filename for the index file
     *
     * @var string
     */
    private $indexFilename;

    /**
     * Constructor
     *
     * @param string $path Path to folder to be used for storing profile snapshots.
     *                     Will be created if it does not exist.
     * @param array $options Additional configuration options for storage
     */
    public function __construct(string $path, array $options = [])
    {
        // Make sure the specified path is writable
        $this->path = $path;
        if (!is_dir($this->path) && @mkdir($this->path, 0777, true) === false && !is_dir($this->path)) {
            throw new RuntimeException(sprintf('Unable to create storage directory for profile snapshots (%s)', $this->path));
        }

        $this->indexFilename = $options['index_filename'] ?? 'index.csv';
    }

    /**
     * @inherit
     */
    public function find(array $criteria = [])
    {
        // Extract search criteria
        $ip = $criteria['ip'] ?? null;
        $url = $criteria['url'] ?? null;
        $limit = $criteria['limit'] ?? null;
        $method = $criteria['method'] ?? null;
        $statusCode = $criteria['statusCode'] ?? null;
        $startTime = (int)($criteria['startTime'] ?? null);
        $endTime = (int)($criteria['endTime'] ?? null);

        // Load the snapshot index file
        $index = $this->getIndexFilename();
        if (!file_exists($index)) {
            return [];
        }
        $index = fopen($index, 'r');
        fseek($index, 0, SEEK_END);

        // Search the index for matching records
        $results = [];
        while (count($results) < $limit && $line = $this->readLineFromFile($index)) {
            $values = str_getcsv($line);
            list($csvToken, $csvIp, $csvMethod, $csvUrl, $csvTime, $csvParent, $csvStatusCode) = $values;
            $csvTime = (int)$csvTime;

            // Check for matches with search criteria
            if ($ip && strpos($csvIp, $ip) === false) {
                continue;
            }
            if ($url && strpos($csvUrl, $url) === false) {
                continue;
            }
            if ($method && strpos($csvMethod, $method) === false) {
                continue;
            }
            if ($statusCode && strpos($csvStatusCode, $statusCode) === false) {
                continue;
            }
            if (empty($startTime) && $csvTime < $startTime) {
                continue;
            }
            if (empty($endTime) && $csvTime > $endTime) {
                continue;
            }

            // If we have reached this point, record is a match
            $result[$csvToken] = [
                'token' => $csvToken,
                'ip' => $csvIp,
                'method' => $csvMethod,
                'url' => $csvUrl,
                'time' => $csvTime,
                'parent' => $csvParent,
                'status_code' => $csvStatusCode,
            ];
        }
        fclose($index);

        // Return matching results
        return array_values($results);
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $token): ?ProfileSnapshot
    {
        if (!$token || !file_exists($snapshotFile = $this->getSnapshotFilename($token))) {
            return  null;
        }

        if (function_exists('gzcompress')) {
            $snapshotFile = 'compress.zlib://' . $snapshotFile;
        }

        return $this->createProfileSnapshotFromData($token, unserialize(file_get_contents($snapshotFile)));
    }

    /**
     * @inheritdoc
     */
    public function write(ProfileSnapshot $profile): bool
    {
        $token = $profile->getToken();

        // Generate filename for the profile snapshot
        $filename = $this->getSnapshotFilename($token);

        // Create folder for this snapshot, if it doesn't already exist
        $fileExists = is_file($filename);
        if (!$fileExists) {
            $directory = dirname($filename);
            if (!is_dir($directory) && false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Unable to create the storage directory (%s).', $directory));
            }
        }

        // Check for cases where the parent and/or child tokens are the same as the current
        // profile token (can occur when there are errors in sub-requests), which would cause
        // an infinite loop
        $parentToken = $profile->getParentToken();
        if ($parentToken === $token) {
            $parentToken = null;
        }
        $childSnapshots = array_map(function (ProfileSnapshot $p) use ($token) {
            return $token !== $p->getToken() ? $p->getToken() : null;
        }, $profile->getChildren());
        $childTokens = array_filter($childSnapshots);

        // Create the profile snapshot file
        $data = [
            'token' => $token,
            'parent' => $parentToken,
            'children' => $childTokens,
            'data' => $profile->getDataCollectors(),
            'ip' => $profile->getIpAddress(),
            'method' => $profile->getHttpMethod(),
            'url' => $profile->getUrl(),
            'time' => $profile->getTime(),
            'status_code' => $profile->getHttpStatusCode(),
        ];

        // Write data to snapshot file
        $context = stream_context_create();
        if (function_exists('gzcompress')) {
            $filename = 'compress.zlib://' . $filename;
            stream_context_set_option($context, 'zlib', 'level', 3);
        }
        if (file_put_contents($filename, serialize($data), 0, $context) === false) {
            return false;
        }

        // Add file to index CSV file
        if (!$fileExists) {
            // Check file is writable
            $file = fopen($this->getIndexFilename(), 'a');
            if ($file === false) {
                return false;
            }

            // Append entry to index file
            fputcsv($file, [
                $profile->getToken(),
                $profile->getIpAddress(),
                $profile->getHttpMethod(),
                $profile->getUrl(),
                $profile->getTime(),
                $profile->getParentToken(),
                $profile->getHttpStatusCode(),
            ]);
            fclose($file);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function purge()
    {
        $flags = FilesystemIterator::SKIP_DOTS;
        $iterator = new RecursiveDirectoryIterator($this->path, $flags);
        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file)) {
                unlink($file);
            } else {
                rmdir($file);
            }
        }
    }

    /**
     * Get the fully qualified path to the index file
     *
     * @return string The index path and filename
     */
    protected function getIndexFilename()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->indexFilename;
    }

    /**
     * Get the fully qualified path to a snapshot file for the supplied token
     *
     * @param string $token
     * @return string The snapshot path and filename
     */
    protected function getSnapshotFilename(string $token)
    {
        // Uses 4 last characters, because first are mostly the same.
        $folderA = substr($token, -2, 2);
        $folderB = substr($token, -4, 2);

        return $this->path . DIRECTORY_SEPARATOR . $folderA . DIRECTORY_SEPARATOR . $folderB . DIRECTORY_SEPARATOR . $token;
    }

    /**
     * Read lines from a text file, from the end of the file back to the beginning. Empty lines
     * are automatically skipped.
     *
     * @param resource $file File resource to read. File must already have the pointer placed at the end of file.
     * @return string|null Line contents, or null if the begninning of the file has been reached
     */
    protected function readLineFromFile($file)
    {
        $line = '';
        $position = ftell($file);
        if ($position === 0) {
            return null;
        }

        while (true) {
            $chunkSize = min($position, 1024);
            $position -= $chunkSize;
            fseek($file, $position);
            if ($chunkSize === 0) {
                // Beginning of file has been reached
                break;
            }

            $buffer = fread($file, $chunkSize);
            $newlinePosition = strrpos($buffer, "\n");
            if ($newlinePosition === false) {
                // Add full contents of buffer to the line and read another chunk
                $line = $buffer . $line;
                continue;
            }

            $position += $newlinePosition;
            $line = substr($buffer, $newlinePosition + 1) . $line;
            fseek($file, max(0, $position), SEEK_SET);
            if ($line !== '') {
                break;
            }
        }

        if ($line === null) {
            return '';
        }
        return $line;
    }

    /**
     * Creates a ProfileSnapshot object based on the supplied data. Also creates parent and child records if required.
     *
     * @param string $token
     * @param array $data
     * @param ProfileSnapshot $parent
     * @return ProfileSnapshot
     */
    protected function createProfileSnapshotFromData(string $token, array $data, ProfileSnapshot $parent = null)
    {
        // Create profile snapshot
        $profile = new ProfileSnapshot($token);
        $profile->setIpAddress($data['ip']);
        $profile->setHttpMethod($data['method']);
        $profile->setHttpStatusCode($data['status_code']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setDataCollectors($data['data']);

        // Add details of the parent snapshot (if it exists)
        if (!$parent && $data['parent']) {
            $parent = $this->read($data['parent']);
        }
        if ($parent) {
            $profile->setParent($parent);
        }

        // Add details of any child snapshots (if they exist)
        foreach ($data['children'] as $token) {
            if (!$token || !file_exists($file = $this->getSnapshotFilename($token))) {
                continue;
            }

            if (\function_exists('gzcompress')) {
                $file = 'compress.zlib://' . $file;
            }

            $profile->addChild($this->createProfileSnapshotFromData($token, unserialize(file_get_contents($file)), $profile));
        }

        return $profile;
    }
}
