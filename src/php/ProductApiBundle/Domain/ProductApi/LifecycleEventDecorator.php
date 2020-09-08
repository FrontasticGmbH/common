<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;

/**
 * Class LifecycleEventDecorator
 *
 * @package Frontastic\Common\ProductApiBundle\Domain\ProductApi
 */
class LifecycleEventDecorator implements ProductApi
{
    use LifecycleTrait;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi
     */
    private $aggregate;

    /**
     * LifecycleEventDecorator constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi $aggregate
     * @param iterable $listeners
     */
    public function __construct(ProductApi $aggregate, iterable $listeners = [])
    {
        $this->aggregate = $aggregate;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @TODO This method should be available on all decorators, extract it into an interface to check for it.
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi
     */
    public function getAggregate(): object
    {
        return $this->aggregate;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category[]
     */
    public function getCategories(CategoryQuery $query): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function queryCategories(CategoryQuery $query): Result
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductType[]
     */
    public function getProductTypes(ProductTypeQuery $query): array
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    public function getProduct($query, string $mode = self::QUERY_SYNC): ?object
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result
     */
    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        return $this->dispatch(__FUNCTION__, func_get_args());
    }
}
