<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Symfony\Component\VarDumper\Caster\CutStub;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Cloner\VarCloner;

abstract class AbstractDataCollector implements DataCollectorInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritDoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * @var ClonerInterface
     */
    private $cloner;

    /**
     * Converts the variable into a serializable Data instance.
     *
     * This array can be displayed in the template using
     * the VarDumper component.
     *
     * @param mixed $var
     *
     * @return Data
     */
    protected function cloneVar($var)
    {
        if ($var instanceof Data) {
            return $var;
        }
        if (null === $this->cloner) {
            $this->cloner = new VarCloner();
            $this->cloner->setMaxItems(-1);
            $this->cloner->addCasters($this->getCasters());
        }

        return $this->cloner->cloneVar($var);
    }

    /**
     * @return callable[] The casters to add to the cloner
     */
    protected function getCasters()
    {
        $casters = [
            '*' => function ($v, array $a, Stub $s, $isNested) {
                if (!$v instanceof Stub) {
                    foreach ($a as $k => $v) {
                        if (\is_object($v) && !$v instanceof \DateTimeInterface && !$v instanceof Stub) {
                            $a[$k] = new CutStub($v);
                        }
                    }
                }

                return $a;
            },
        ] + ReflectionCaster::UNSET_CLOSURE_FILE_INFO;

        return $casters;
    }

    /**
     * Define properties to be serialised
     *
     * @return array
     */
    public function __sleep()
    {
        return ['data'];
    }

    /**
     * Define properties to be populated when unserialised
     */
    public function __wakeup()
    {
    }

    /**
     * @internal to prevent implementing \Serializable
     */
    final protected function serialize()
    {
    }

    /**
     * @internal to prevent implementing \Serializable
     */
    final protected function unserialize($data)
    {
    }
}
