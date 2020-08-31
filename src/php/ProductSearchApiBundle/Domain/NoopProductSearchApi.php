<?php


namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

class NoopProductSearchApi implements ProductSearchApi
{
    public function query(ProductQuery $query): PromiseInterface
    {
        $promise = new Promise();

        $promise->resolve(new Result());

        return $promise;
    }

    public function getSearchableAttributes(): array
    {
        return [];
    }

    public function getDangerousInnerClient()
    {
        return null;
    }
}
