<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use GuzzleHttp\Promise\PromiseInterface;

class LifecycleEventDecorator implements ProductSearchApi
{
    use LifecycleTrait;

    /** @var ProductSearchApi */
    private $aggregate;

    public function __construct(ProductSearchApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    public function getAggregate(): ProductSearchApi
    {
        return $this->aggregate;
    }

    public function query(ProductQuery $query): PromiseInterface
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getSearchableAttributes(): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
