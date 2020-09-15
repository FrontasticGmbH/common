<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\PromiseInterface;

abstract class ProductApiBase implements ProductApi
{
    /** @var ProductSearchApi */
    private $productSearchApi;

    public function __construct(ProductSearchApi $productSearchApi)
    {
        $this->productSearchApi = $productSearchApi;
    }

    final public function getCategories(CategoryQuery $query): array
    {
        return $this->queryCategoriesImplementation($query)->items;
    }

    final public function queryCategories(CategoryQuery $query): Result
    {
        return $this->queryCategoriesImplementation($query);
    }

    final public function getProductTypes(ProductTypeQuery $query): array
    {
        return $this->getProductTypesImplementation($query);
    }

    final public function getProduct($originalQuery, string $mode = self::QUERY_SYNC): ?object
    {
        $query = ProductApi\Query\SingleProductQuery::fromLegacyQuery($originalQuery);
        $query->validate();

        $promise = $this->getProductImplementation($query);
        return $mode === self::QUERY_SYNC ? $promise->wait() : $promise;
    }

    final public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $promise = $this->productSearchApi->query($query);
        return $mode === self::QUERY_SYNC ? $promise->wait() : $promise;
    }

    abstract protected function queryCategoriesImplementation(CategoryQuery $query): Result;

    abstract protected function getProductTypesImplementation(ProductTypeQuery $query): array;

    abstract protected function getProductImplementation(SingleProductQuery $query): PromiseInterface;
}
