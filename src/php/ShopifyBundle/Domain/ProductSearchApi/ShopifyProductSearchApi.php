<?php

namespace Frontastic\Common\ShopifyBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use GuzzleHttp\Promise\PromiseInterface;

class ShopifyProductSearchApi extends ProductSearchApiBase
{
    private const DEFAULT_VARIANTS_TO_FETCH = 250;
    private const DEFAULT_ELEMENTS_TO_FETCH = 10;

    /**
     * @var ShopifyClient
     */
    private $client;

    /**
     * @var ShopifyProductMapper
     */
    private $productMapper;

    public function __construct(ShopifyClient $client, ShopifyProductMapper $productMapper)
    {
        $this->client = $client;
        $this->productMapper = $productMapper;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $productQuery = "
            id
            title
            description
            descriptionHtml
            handle
            productType
            tags
            vendor
            createdAt
            updatedAt
            collections(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                edges {
                    node {
                        id
                    }
                }
            }
            metafields(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                edges {
                    node {
                        {$this->getMetafieldQueryFields()}
                    }
                }
            }
            seo {
                description
                title
            }
            variants(first: " . self::DEFAULT_VARIANTS_TO_FETCH . ") {
                edges {
                    node {
                        id
                        sku
                        title
                        availableForSale
                        quantityAvailable
                        metafields(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getMetafieldQueryFields()}
                                }
                            }
                        }
                        priceV2 {
                            amount
                            currencyCode
                        }
                        product {
                            id
                            images(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                                edges {
                                    node {
                                        originalSrc
                                    }
                                }
                            }
                        }
                        selectedOptions {
                            name
                            value
                        }
                        image {
                            originalSrc
                        }
                    }
                }
            }
        ";

        $parameters = [];

        if ($query->query) {
            $parameters[] = "$query->query";
        }

        if ($query->filter) {
            foreach ($query->filter as $queryFilter) {
                $parameters[] = $this->productMapper->toFilterString($queryFilter);
            }
        }

        if ($query->productType) {
            $parameters[] = sprintf('product_type:%s', $query->productType);
        }

        if ($query->facets) {
            foreach ($query->facets as $facet) {
                if ($facet->type == Facets::TYPE_TERM) {
                    $parameters[] = sprintf(
                        '(%s:%s)',
                        $facet->handle,
                        implode(" OR ", $facet->terms ?? [])
                    );
                }
            }
        }

        $skus = [];
        if ($query->sku !== null) {
            $skus[] = $query->sku;
        }
        if ($query->skus !== null) {
            $skus = array_merge($skus, $query->skus);
        }

        if (count($skus)) {
            $parameters[] = "(" . implode(' OR ', $skus) .")";
        }

        $queryFilter = "query:\"" . implode(' AND ', $parameters) . "\"";

        $pageFilter = $this->buildPageFilter($query);

        $query->query = "{
            products($pageFilter $queryFilter) {
                edges {
                    cursor
                    node {
                        $productQuery
                    }
                }
                pageInfo {
                  hasNextPage
                  hasPreviousPage
                }
            }
        }";

        $productIds = [];
        if ($query->productId !== null) {
            $productIds[] = $query->productId;
        }
        if ($query->productIds !== null) {
            $productIds = array_merge($productIds, $query->productIds);
        }

        if (count($parameters) && count($productIds)) {
            throw new \InvalidArgumentException(
                'Currently it is not possible to filter by products and other parameters at the same time'
            );
        }

        if (count($productIds)) {
            $query->query = "{
                nodes(ids: [\"" . implode("\",\"", $productIds) . "\"]) {
                    id
                    ... on Product {
                        $productQuery
                    }
                }
            }";
        }

        if (count($parameters) && count($productIds) && $query->category) {
            throw new \InvalidArgumentException(
                'Currently it is not possible to filter by category and other parameters at the same time'
            );
        }

        if ($query->category) {
            $query->query = "{
                node(id: \"{$query->category}\") {
                    id
                    ... on Collection {
                        products($pageFilter) {
                            edges {
                                cursor
                                node {
                                    $productQuery
                                }
                            }
                            pageInfo {
                              hasNextPage
                              hasPreviousPage
                            }
                        }
                    }
                }
            }";
        }

        return $this->client
            ->request($query->query, $query->locale)
            ->then(function ($result) use ($query): Result {
                $hasNextPage = null;
                $hasPreviousPage = null;

                $products = [];
                $productsData = [];
                $pageInfoData = [];

                if ($result['errors']
                    && strpos($result['errors'][0]['message'], 'Invalid global id') !== false
                ) {
                    return new Result([
                        'query' => clone $query,
                    ]);
                }

                if (key_exists('nodes', $result['body']['data'])) {
                    $productsData = $result['body']['data']['nodes'];
                }

                if (key_exists('node', $result['body']['data']) &&
                    key_exists('products', $result['body']['data']['node'])) {
                    $productsData = $result['body']['data']['node']['products']['edges'];
                    $pageInfoData = $result['body']['data']['node']['products']['pageInfo'];
                }

                if (key_exists('products', $result['body']['data'])) {
                    $productsData = $result['body']['data']['products']['edges'];
                    $pageInfoData = $result['body']['data']['products']['pageInfo'];
                }

                if (!empty($pageInfoData)) {
                    $hasNextPage = $pageInfoData['hasNextPage'];
                    $hasPreviousPage = $pageInfoData['hasPreviousPage'];
                }

                $previousCursor = $productsData[0]['cursor'] ?? null;

                $nextCursor = null;
                foreach ($productsData as $productData) {
                    $products[] = $this->productMapper->mapDataToProduct(
                        $productData['node'] ?? $productData,
                        $query
                    );
                    $nextCursor = $productData['cursor'] ?? null;
                }

                return new Result([
                    // "total" is not available in Shopify.
                    'previousCursor' => $hasPreviousPage ? "before:\"$previousCursor\"" : null,
                    'nextCursor' => $hasNextPage ? "after:\"$nextCursor\"" : null,
                    'count' => count($products),
                    'items' => $products,
                    'facets' => $this->productMapper->mapDataToFacets($productsData),
                    'query' => clone $query,
                ]);
            });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        $query = "
            query {
                productTags(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                    edges {
                        node
                    }
                }
            }";

        return $this->client
            ->request($query)
            ->then(function (array $result): array {
                if ($result['errors']) {
                    throw new \RuntimeException($result['errors'][0]['message']);
                }

                return $this->productMapper->mapDataToProductAttributes($result['body']['data']);
            });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function buildPageFilter(PaginatedQuery $query): string
    {
        $pageFilter = strpos($query->cursor, 'before:') === 0 ? "last:" : "first:";
        $pageFilter .= $query->limit;
        $pageFilter .= !empty($query->cursor) ? ' ' . $query->cursor : null;

        return $pageFilter;
    }

    private function getMetafieldQueryFields(): string
    {
        return '
            id
            key
            value
            valueType
        ';
    }
}
