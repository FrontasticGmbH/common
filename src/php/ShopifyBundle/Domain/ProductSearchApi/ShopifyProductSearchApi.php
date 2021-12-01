<?php

namespace Frontastic\Common\ShopifyBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ShopifyBundle\Domain\Exception\QueryException;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use GuzzleHttp\Promise\PromiseInterface;

class ShopifyProductSearchApi extends ProductSearchApiBase
{
    private const MAX_ELEMENTS_TO_FETCH = 250;
    private const DEFAULT_ELEMENTS_TO_FETCH = 10;

    const PRODUCT_QUERY_FIELDS_LABEL = 'productQueryFields';
    const COLLECTION_QUERY_FIELDS_LABEL = 'collectionQueryFields';
    const METAFIELD_QUERY_FIELDS_LABEL = 'metafieldQueryFields';
    const SEO_QUERY_FIELDS_LABEL = 'seoQueryFields';
    const VARIANT_QUERY_FIELDS_LABEL = 'variantQueryFields';
    const PRICE_V2_QUERY_FIELDS_LABEL = 'priceV2QueryFields';
    const IMAGE_QUERY_FIELDS_LABEL = 'imageQueryFields';
    const SELECTED_OPTION_QUERY_FIELDS_LABEL = 'selectedOptionQueryFields';

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
        $query->rawApiInput = (array)$query->rawApiInput;

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

        $queryString = "{
            products($pageFilter $queryFilter) {
                edges {
                    cursor
                    node {
                        {$this->getProductQueryFields($query)}
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
            $queryString = "{
                nodes(ids: [\"" . implode("\",\"", $productIds) . "\"]) {
                    id
                    ... on Product {
                        {$this->getProductQueryFields($query)}
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
            $queryString = "{
                node(id: \"{$query->category}\") {
                    id
                    ... on Collection {
                        products($pageFilter) {
                            edges {
                                cursor
                                node {
                                    {$this->getProductQueryFields($query)}
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
            ->request($queryString, $query->locale)
            ->then(function ($result) use ($query, $skus): Result {
                $hasNextPage = null;
                $hasPreviousPage = null;

                $products = [];
                $productsData = [];
                $pageInfoData = [];

                if (!is_array($result['body'])) {
                    throw new \Exception('Empty body response');
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
                    if (!is_array($productData)) {
                        continue;
                    }

                    $product = $this->productMapper->mapDataToProduct(
                        $productData['node'] ?? $productData,
                        $query
                    );

                    // Shopify might return products that has the SKU string as part of other fields so we need to
                    // make sure that skip any product that does not contain the given SKUs, if any.
                    if ($skus && !$this->hasProductAnyVariantWithSkus($product, $skus)) {
                        // Skip product if does not contain any of the SKUs to be filtered by.
                        continue;
                    }

                    $products[] = $product;
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
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof QueryException &&
                    (strpos($exception->getMessage(), 'Invalid global id') !== false)
                ) {
                    return new Result([
                        'query' => clone $query,
                    ]);
                }
                throw $exception;
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
                return $this->productMapper->mapDataToProductAttributes($result['body']['data']);
            });
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function getRawApiInputField(array $rawApiInput, string $field): string
    {
        return key_exists($field, $rawApiInput) ? $rawApiInput[$field] : '';
    }

    private function buildPageFilter(PaginatedQuery $query): string
    {
        $pageFilter = strpos($query->cursor, 'before:') === 0 ? "last:" : "first:";
        $pageFilter .= min($query->limit, self::MAX_ELEMENTS_TO_FETCH);
        $pageFilter .= !empty($query->cursor) ? ' ' . $query->cursor : null;

        return $pageFilter;
    }

    private function getProductQueryFields(Query $query): string
    {
        return "
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
                        {$this->getCollectionQueryFields($query)}
                    }
                }
            }
            metafields(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                edges {
                    node {
                        {$this->getMetafieldQueryFields($query)}
                    }
                }
            }
            seo {
                {$this->getSeoQueryFields($query)}
            }
            variants(first: " . self::MAX_ELEMENTS_TO_FETCH . ") {
                edges {
                    node {
                        {$this->getVariantQueryFields($query)}

                    }
                }
            }
            {$this->getRawApiInputField($query->rawApiInput, self::PRODUCT_QUERY_FIELDS_LABEL)}
        ";
    }

    private function getCollectionQueryFields(Query $query): string
    {
        return "
            id
            {$this->getRawApiInputField($query->rawApiInput, self::COLLECTION_QUERY_FIELDS_LABEL)}
        ";
    }

    private function getMetafieldQueryFields(Query $query): string
    {
        return "
            id
            key
            value
            type
            {$this->getRawApiInputField($query->rawApiInput, self::METAFIELD_QUERY_FIELDS_LABEL)}
        ";
    }

    private function getSeoQueryFields(Query $query): string
    {
        return "
            description
            title
            {$this->getRawApiInputField($query->rawApiInput, self::SEO_QUERY_FIELDS_LABEL)}
        ";
    }


    private function getVariantQueryFields(Query $query): string
    {
        return "
            id
            sku
            title
            availableForSale
            quantityAvailable
            metafields(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                edges {
                    node {
                        {$this->getMetafieldQueryFields($query)}
                    }
                }
            }
            priceV2 {
                {$this->getPriceV2QueryFields($query)}
            }
            compareAtPriceV2 {
                {$this->getPriceV2QueryFields($query)}
            }
            product {
                id
                images(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                    edges {
                        node {
                            {$this->getImageQueryFields($query)}
                        }
                    }
                }
            }
            selectedOptions {
                {$this->getSelectedOptionQueryFields($query)}
            }
            image {
                {$this->getImageQueryFields($query)}
            }
            {$this->getRawApiInputField($query->rawApiInput, self::VARIANT_QUERY_FIELDS_LABEL)}
        ";
    }

    private function getPriceV2QueryFields(Query $query): string
    {
        return "
            amount
            currencyCode
            {$this->getRawApiInputField($query->rawApiInput, self::PRICE_V2_QUERY_FIELDS_LABEL)}
       ";
    }

    private function getImageQueryFields(Query $query): string
    {
        return "
            originalSrc
            {$this->getRawApiInputField($query->rawApiInput, self::IMAGE_QUERY_FIELDS_LABEL)}
       ";
    }

    private function getSelectedOptionQueryFields(Query $query): string
    {
        return "
            name
            value
            {$this->getRawApiInputField($query->rawApiInput, self::SELECTED_OPTION_QUERY_FIELDS_LABEL)}
       ";
    }

    private function hasProductAnyVariantWithSkus(Product $product, array $skus): bool
    {
        foreach ($product->variants as $variant) {
            if (in_array($variant->sku, $skus)) {
                return true;
            }
        }

        return false;
    }
}
