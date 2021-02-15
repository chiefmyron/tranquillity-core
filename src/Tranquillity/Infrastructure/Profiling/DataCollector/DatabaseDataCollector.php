<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Tranquillity\Infrastructure\Profiling\Caster\ObjectParameter;

class DatabaseDataCollector extends AbstractDataCollector implements DataCollectorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DebugStack
     */
    private $logger;

    /**
     * Constructor
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->logger = $this->connection->getConfiguration()->getSQLLogger();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'database';
    }

    /**
     * @inheritdoc
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $this->data = [
            'queries' => $this->sanitiseQueries($this->logger->queries),
            'connection' => $this->connection->getConfiguration()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    private function sanitiseQueries(array $queries): array
    {
        foreach ($queries as $i => $query) {
            $queries[$i] = $this->sanitiseQuery($query);
        }

        return $queries;
    }

    private function sanitiseQuery(array $query): array
    {
        $query['explainable'] = true;
        $query['runnable'] = true;
        if (null === $query['params']) {
            $query['params'] = [];
        }
        if (!\is_array($query['params'])) {
            $query['params'] = [$query['params']];
        }
        if (!\is_array($query['types'])) {
            $query['types'] = [];
        }
        foreach ($query['params'] as $j => $param) {
            $e = null;
            if (isset($query['types'][$j])) {
                // Transform the param according to the type
                $type = $query['types'][$j];
                if (\is_string($type)) {
                    $type = Type::getType($type);
                }
                if ($type instanceof Type) {
                    $query['types'][$j] = $type->getBindingType();
                    try {
                        $param = $type->convertToDatabaseValue($param, $this->connection->getDatabasePlatform());
                    } catch (\TypeError $e) {
                    } catch (ConversionException $e) {
                    }
                }
            }

            list($query['params'][$j], $explainable, $runnable) = $this->sanitizeParam($param, $e);
            if (!$explainable) {
                $query['explainable'] = false;
            }

            if (!$runnable) {
                $query['runnable'] = false;
            }
        }

        $query['params'] = $this->cloneVar($query['params']);

        return $query;
    }

    /**
     * Sanitizes a param.
     *
     * The return value is an array with the sanitized value and a boolean
     * indicating if the original value was kept (allowing to use the sanitized
     * value to explain the query).
     */
    private function sanitizeParam($var, ?\Throwable $error): array
    {
        if (\is_object($var)) {
            return [$o = new ObjectParameter($var, $error), false, $o->isStringable() && !$error];
        }

        if ($error) {
            return ['⚠ ' . $error->getMessage(), false, false];
        }

        if (\is_array($var)) {
            $a = [];
            $explainable = $runnable = true;
            foreach ($var as $k => $v) {
                list($value, $e, $r) = $this->sanitizeParam($v, null);
                $explainable = $explainable && $e;
                $runnable = $runnable && $r;
                $a[$k] = $value;
            }

            return [$a, $explainable, $runnable];
        }

        if (\is_resource($var)) {
            return [sprintf('/* Resource(%s) */', get_resource_type($var)), false, false];
        }

        return [$var, true, true];
    }
}
