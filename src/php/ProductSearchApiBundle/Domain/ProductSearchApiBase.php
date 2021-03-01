<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\PaginationAdapter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use GuzzleHttp\Promise\PromiseInterface;

abstract class ProductSearchApiBase implements ProductSearchApi
{
    final public function query(ProductQuery $query): PromiseInterface
    {
        /** @var ProductQuery $query */
        $query = PaginationAdapter::queryCursorToOffset($query);

        return $this->queryImplementation($query)
            ->then(function (Result $result): Result {
                return PaginationAdapter::resultOffsetToCursor($result);
            });
    }

    final public function getSearchableAttributes(): PromiseInterface
    {
        return $this->getSearchableAttributesImplementation();
    }

    abstract protected function queryImplementation(ProductQuery $query): PromiseInterface;

    abstract protected function getSearchableAttributesImplementation(): PromiseInterface;
}
