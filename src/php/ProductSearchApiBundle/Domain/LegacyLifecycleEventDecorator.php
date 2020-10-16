<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\LifecycleTrait;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * This lifecycle event decorator calls the listeners for the old `ProductApi::query()` method.
 */
class LegacyLifecycleEventDecorator implements ProductSearchApi
{
    use LifecycleTrait;

    /** @var ProductSearchApi */
    private $aggregate;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ?ProductApi
     */
    private $productApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProductSearchApi $aggregate,
        ContainerInterface $container,
        LoggerInterface $logger,
        iterable $listeners = []
    )
    {
        $this->aggregate = $aggregate;
        $this->container = $container;
        $this->logger = $logger;

        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }
    }

    public function getAggregate(): ProductSearchApi
    {
        return $this->aggregate;
    }

    /**
     * This handles the actual BC case by getting the ProductAPI from Catwalk and dispatching its listeners.
     */
    protected function getAggregateForListeners(): ProductApi
    {
        $this->logger->notice('Decorating ProductApi::query() is deprecated. Migrate to ProductSearchApi::query() instead.');
        if ($this->productApi === null) {
            $this->productApi = $this->container->get('frontastic.catwalk.product_api');
        }
        return $this->productApi;
    }

    public function query(ProductQuery $query): PromiseInterface
    {
        return $this->dispatch(__FUNCTION__, [$query, ProductApi::QUERY_ASYNC]);
    }

    public function getSearchableAttributes(): PromiseInterface
    {
        return $this->aggregate->getSearchableAttributes();
    }
}
