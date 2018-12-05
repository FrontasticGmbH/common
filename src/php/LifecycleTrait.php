<?php

namespace Frontastic\Common;

/**
 * Trait LifecycleTrait
 *
 * @package Frontastic\Common
 */
trait LifecycleTrait
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param mixed $listener
     */
    protected function addListener($listener): void
    {
        $this->listeners[] = $listener;
    }

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return mixed
     */
    public function getDangerousInnerClient()
    {
        return $this->getAggregate()->getDangerousInnerClient();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    protected function dispatch(string $method, array $arguments)
    {
        $beforeEvent = 'before' . ucfirst($method);
        foreach ($this->listeners as $listener) {
            if (is_callable([$listener, $beforeEvent])) {
                call_user_func_array([$listener, $beforeEvent], array_merge([$this->getAggregate()], $arguments));
            }
        }

        $result = call_user_func_array([$this->getAggregate(), $method], $arguments);

        $afterEvent = 'after' . ucfirst($method);
        foreach ($this->listeners as $listener) {
            if (is_callable([$listener, $afterEvent])) {
                $listener->$afterEvent($this->getAggregate(), $result);
            }
        }

        return $result;
    }

    /**
     * @return object
     */
    abstract protected function getAggregate(): object;
}
