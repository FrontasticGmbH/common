<?php

namespace Frontastic\Common;

use Frontastic\Common\ProductSearchApiBundle\Domain\LegacyLifecycleEventDecorator;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use phpDocumentor\Reflection\Types\Object_;

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
                $newArguments = call_user_func_array(
                    [$listener, $beforeEvent],
                    array_merge([$this->getAggregateForListeners()], $arguments)
                );

                if (is_array($newArguments)) {
                    $arguments = $newArguments;
                }
            }
        }

        $rawResult = call_user_func_array([$this->getAggregateForRawResult(), $method], $arguments);
        if ($rawResult instanceof PromiseInterface) {
            $resultPromise = $rawResult;
            $returnPromise = true;
        } else {
            $resultPromise = new FulfilledPromise($rawResult);
            $returnPromise = false;
        }

        $resultPromise = $resultPromise->then(function ($result) use ($method) {
            $afterEvent = 'after' . ucfirst($method);
            foreach ($this->listeners as $listener) {
                if (is_callable([$listener, $afterEvent])) {
                    $returnValue = $listener->$afterEvent($this->getAggregateForListeners(), $result);

                    // If a listerner changes the return value, for example
                    // replacing the default return object with an enriched custom
                    // object we use this as a return value for now. The return
                    // type hints ensure this will stay valid.
                    if ($returnValue) {
                        $result = $returnValue;
                    }
                }
            }

            return $result;
        });

        if ($returnPromise) {
            return $resultPromise;
        }
        return $resultPromise->wait();
    }

    /**
     * Used for BC purposes in {@link LegacyLifecycleEventDecorator}.
     */
    protected function getAggregateForRawResult(): object
    {
        return $this->getAggregate();
    }

    /**
     * Used for BC purposes in {@link LegacyLifecycleEventDecorator}.
     */
    protected function getAggregateForListeners(): object
    {
        return $this->getAggregate();
    }

    /**
     * @return object
     */
    abstract protected function getAggregate(): object;
}
