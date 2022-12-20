<?php

namespace Frontastic\Common\ShopifyBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\ProductNotFoundException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyIdMapper;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ProductSearchApi\ShopifyProductSearchApi;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use GuzzleHttp\Promise\PromiseInterface;

class ShopifyProductApi extends ProductApiBase
{
    private const DEFAULT_PRODUCT_TYPES_TO_FETCH = 10;
    private const MAX_ELEMENTS_TO_FETCH = 250;

    const COLLECTION_QUERY_FIELDS_LABEL = 'collectionQueryFields';
    const PRODUCT_TYPE_QUERY_FIELDS_LABEL = 'productTypeQueryFields';

    /**
     * @var ShopifyClient
     */
    private $client;

    /**
     * @var ShopifyProductMapper
     */
    private $productMapper;

    /**
     * @var ShopifyProductSearchApi
     */
    private $shopifyProductSearchApi;

    public function __construct(
        ShopifyClient $client,
        ProductSearchApi $productSearchApi,
        ShopifyProductMapper $productMapper
    ) {
        parent::__construct($productSearchApi);

        $this->client = $client;
        $this->productMapper = $productMapper;

        $this->shopifyProductSearchApi = new ShopifyProductSearchApi($client, $productMapper);
    }

    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        $query->rawApiInput = (array)$query->rawApiInput;

        $filters = [];

        if ($query->slug) {
            $filters[] = "query:\"'$query->slug'\"";
        }

        $pageFilter = $this->buildPageFilter($query);

        $queryString = "{
            collections($pageFilter, " . implode(' , ', $filters) . ") {
                edges {
                    cursor
                    node {
                        {$this->getCollectionQueryFields($query)}
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                }
            }
        }";

        $result = $this->client->request($queryString)->wait();

        $previousCursor = null;
        $nextCursor = null;
        $hasNextPage = null;
        $hasPreviousPage = null;
        $categories = [];

        if (key_exists('collections', $result['body']['data'])) {
            $collectionsData = $result['body']['data']['collections']['edges'];
            $hasNextPage = $result['body']['data']['collections']['pageInfo']['hasNextPage'];
            $hasPreviousPage = $result['body']['data']['collections']['pageInfo']['hasPreviousPage'];

            $previousCursor = $collectionsData[0]['cursor'] ?? null;

            foreach ($collectionsData as $collectionData) {
                $categories[] = new Category([
                    'categoryId' => ShopifyIdMapper::mapDataToId($collectionData['node']['id']),
                    'name' => $collectionData['node']['title'],
                    'slug' => $collectionData['node']['handle'],
                    'path' => '/' . ShopifyIdMapper::mapDataToId($collectionData['node']['id']),
                    'dangerousInnerCategory' => $query->loadDangerousInnerData ? $collectionData : null,
                ]);

                $nextCursor = $collectionData['cursor'] ?? null;
            }
        }

        return new Result([
            // @TODO: "total" is not available in Shopify.
            'previousCursor' => $hasPreviousPage ? "before:\"$previousCursor\"" : null,
            'nextCursor' => $hasNextPage ? "after:\"$nextCursor\"" : null,
            'count' => count($categories),
            'items' => $categories,
            'query' => clone $query,
        ]);
    }

    protected function getProductTypesImplementation(ProductTypeQuery $query): array
    {
        $queryString = "{
            productTypes(first: " . self::DEFAULT_PRODUCT_TYPES_TO_FETCH . ") {
                edges {
                    {$this->getProductTypeQueryFields($query)}
                }
            }
        }";

        $result = $this->client->request($queryString)->wait();

        $productTypes = [];
        foreach ($result['body']['data']['productTypes']['edges'] as $productTypeData) {
            if ($productTypeData['cursor'] === '' || $productTypeData['node'] == '') {
                continue;
            }

            $productTypes[] = new ProductType([
                'productTypeId' => $productTypeData['node'],
                'name' => $productTypeData['node'],
                'dangerousInnerProductType' => $this->productMapper->dataToDangerousInnerData(
                    $productTypeData,
                    $query
                ),
            ]);
        }

        return $productTypes;
    }

    protected function getProductImplementation(SingleProductQuery $query): PromiseInterface
    {
        if ($query->sku) {
            return $this->shopifyProductSearchApi
                ->query(
                    new ProductQuery([
                        'skus' => [$query->sku],
                        'locale' => $query->locale,
                        'loadDangerousInnerData' => $query->loadDangerousInnerData,
                        'rawApiInput' => $query->rawApiInput,
                    ])
                )
                ->then(
                    function (Result $productQueryResult) use ($query) {
                        if (count($productQueryResult->items) === 0) {
                            throw ProductNotFoundException::bySku($query->sku);
                        }
                        return reset($productQueryResult->items);
                    }
                );
        }

        return $this->shopifyProductSearchApi
            ->query(
                new ProductQuery([
                    'productIds' => [$query->productId],
                    'locale' => $query->locale,
                    'loadDangerousInnerData' => $query->loadDangerousInnerData,
                    'rawApiInput' => $query->rawApiInput,
                ])
            )
            ->then(
                function (Result $productQueryResult) use ($query) {
                    if (count($productQueryResult->items) === 0) {
                        throw ProductNotFoundException::byProductId($query->productId);
                    }
                    return reset($productQueryResult->items);
                }
            );
    }

    public function getDangerousInnerClient(): ShopifyClient
    {
        return $this->client;
    }

    private function getRawApiInputField(array $rawApiInput, string $field): string
    {
        return key_exists($field, $rawApiInput) ? $rawApiInput[$field] : '';
    }

    private function getCollectionQueryFields(Query $query): string
    {
        return "
            id
            title
            handle
            {$this->getRawApiInputField($query->rawApiInput, self::COLLECTION_QUERY_FIELDS_LABEL)}
        ";
    }

    private function getProductTypeQueryFields(Query $query): string
    {
        return "
            cursor
            node
            {$this->getRawApiInputField($query->rawApiInput, self::PRODUCT_TYPE_QUERY_FIELDS_LABEL)}
        ";
    }

    private function buildPageFilter(PaginatedQuery $query): string
    {
        $pageFilter = strpos($query->cursor, 'before:') === 0 ? "last:" : "first:";
        $pageFilter .= min($query->limit, self::MAX_ELEMENTS_TO_FETCH);
        $pageFilter .= !empty($query->cursor) ? ' ' . $query->cursor : null;

        return $pageFilter;
    }
}
