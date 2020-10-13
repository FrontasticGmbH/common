<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use GuzzleHttp\Promise\PromiseInterface;

abstract class ProductSearchApiBase implements ProductSearchApi
{
    final public function query(ProductQuery $query): PromiseInterface
    {
        return $this->queryImplementation($query);
    }

    final public function getSearchableAttributes(): PromiseInterface
    {
        return $this->getSearchableAttributesImplementation();
    }

    abstract protected function queryImplementation(ProductQuery $query): PromiseInterface;

    abstract protected function getSearchableAttributesImplementation(): PromiseInterface;
}
