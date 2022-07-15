<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\PaginationAdapter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class ProductSearchApiBase implements ProductSearchApi
{
    private ?LoggerInterface $logger = null;

    final public function query(ProductQuery $query): PromiseInterface
    {
        /** @var ProductQuery $query */
        $query = PaginationAdapter::queryCursorToOffset($query);

        //Backwards compatibility for customer implementations
        if (!$query->category && $query->categories) {
            $query->category = array_shift($query->categories);
        }

        return $this->queryImplementation($query)
            ->then(function (Result $result): Result {
                return PaginationAdapter::resultOffsetToCursor($result);
            });
    }

    final public function getSearchableAttributes(): PromiseInterface
    {
        return $this->getSearchableAttributesImplementation();
    }

    final public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    final protected function getLogger():LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }
        return $this->logger;
    }

    abstract protected function queryImplementation(ProductQuery $query): PromiseInterface;

    abstract protected function getSearchableAttributesImplementation(): PromiseInterface;
}
