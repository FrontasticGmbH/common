<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use GuzzleHttp\Promise\PromiseInterface;

interface ProductApi
{
    /**
     * @TODO Deprecate the sync version because of the added complexity. It makes the interface odd to use and the
     *     async version can be made synchronous by calling `.wait()`.
     */
    const QUERY_SYNC = 'sync';
    const QUERY_ASYNC = 'async';

    /**
     * @param CategoryQuery $query
     * @return Category[]
     * @deprecated Use `queryCategories()` instead
     */
    public function getCategories(CategoryQuery $query): array;

    /**
     * @param CategoryQuery $query
     * @return Result
     */
    public function queryCategories(CategoryQuery $query): object;

    /**
     * @param ProductTypeQuery $query
     * @return ProductType[]
     */
    public function getProductTypes(ProductTypeQuery $query): array;

    /**
     * @param SingleProductQuery $query This might also be a `ProductQuery` for backwards compatibility reasons.
     * @param string $mode One of the QUERY_* connstants. Execute the query synchronously or asynchronously?
     * @return Product|PromiseInterface|null A product or null when the mode is sync and a promise if the mode is async.
     */
    public function getProduct($query, string $mode = self::QUERY_SYNC): ?object;

    /**
     * @param ProductQuery $query
     * @param string $mode One of the QUERY_* connstants. Execute the query synchronously or asynchronously?
     * @return Result|PromiseInterface A result when the mode is sync and a promise if the mode is async.
     */
    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object;

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
    public function getDangerousInnerClient();
}
