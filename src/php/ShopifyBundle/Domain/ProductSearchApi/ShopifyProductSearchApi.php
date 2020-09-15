<?php

namespace Frontastic\Common\ShopifyBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;

class ShopifyProductSearchApi extends ProductSearchApiBase
{
    private const DEFAULT_VARIANTS_TO_FETCH = 1;
    private const DEFAULT_COLLECTIONS_TO_FETCH = 10;

    /**
     * @var ShopifyClient
     */
    private $client;

    public function __construct(ShopifyClient $client)
    {
        $this->client = $client;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $productQuery = "
            id
            title
            description
            handle
            updatedAt
            collections(first: " . self::DEFAULT_COLLECTIONS_TO_FETCH . ") {
                edges {
                    node {
                        id
                    }
                }
            }
            variants(first: " . self::DEFAULT_VARIANTS_TO_FETCH . ") {
                edges {
                    node {
                        id
                        sku
                        title
                        currentlyNotInStock
                        priceV2 {
                            amount
                            currencyCode
                        }
                        product {
                            id
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

        if ($query->category) {
            $parameters[] = "$query->category";
        }

        $skus = [];
        if ($query->sku !== null) {
            $skus[] = $query->sku;
        }
        if ($query->skus !== null) {
            $skus = array_merge($skus, $query->skus);
        }

        if (count($skus)) {
            $parameters = array_merge($parameters, $skus);
        }

        $queryFilter = "query:\"" . implode(' OR ', $parameters) . "\"";

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

        return $this->client
            ->request($query->query, $query->locale)
            ->then(function ($result) use ($query): Result {
                $hasNextPage = null;
                $hasPreviousPage = null;

                $products = [];
                $productsData = [];

                if ($result['errors']
                    && strpos($result['errors'][0]['message'], 'Invalid global id') !== false
                ) {
                    return new Result([
                        'query' => clone $query,
                    ]);
                }

                if (key_exists('products', $result['body']['data'])) {
                    $productsData = $result['body']['data']['products']['edges'];
                    $hasNextPage = $result['body']['data']['products']['pageInfo']['hasNextPage'];
                    $hasPreviousPage = $result['body']['data']['products']['pageInfo']['hasPreviousPage'];
                }

                if (key_exists('nodes', $result['body']['data'])) {
                    $productsData = $result['body']['data']['nodes'];
                }

                $previousCursor = $productsData[0]['cursor'] ?? null;

                $nextCursor = null;
                foreach ($productsData as $key => $productData) {
                    $products[] = $this->mapDataToProduct($productData['node'] ?? $productData);
                    $nextCursor = $productData['cursor'] ?? null;
                }

                return new Result([
                    // @TODO: "total" is not available in Shopify.
                    'previousCursor' => $hasPreviousPage ? "before:\"$previousCursor\"" : null,
                    'nextCursor' => $hasNextPage ? "after:\"$nextCursor\"" : null,
                    'count' => count($products),
                    'items' => $products,
                    'query' => clone $query,
                ]);
            });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        return new FulfilledPromise([]);
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

    private function mapDataToProduct(array $productData): Product
    {
        return new Product([
            'productId' => $productData['id'],
            'name' => $productData['title'],
            'description' => $productData['description'],
            'slug' => $productData['handle'],
            'categories' => array_map(
                function (array $category) {
                    return $category['node']['id'];
                },
                $productData['collections']['edges']
            ),
            'changed' => $this->parseDate($productData['updatedAt']),
            'variants' => $this->mapDataToVariants($productData['variants']['edges']),
            // @TODO Include dangerousInnerProduct base on locale flag
            // 'dangerousInnerProduct' => $productData,
        ]);
    }

    private function parseDate(string $string): \DateTimeImmutable
    {
        $formats = [
            'Y-m-d\TH:i:s.uP',
            \DateTimeInterface::RFC3339,
            \DateTimeInterface::RFC3339_EXTENDED,
        ];

        foreach ($formats as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $string);
            if ($date !== false) {
                return $date;
            }
        }

        throw new \RuntimeException('Invalid date: ' . $string);
    }

    private function mapDataToVariants(array $variantsData): array
    {
        $variants = [];
        foreach ($variantsData as $variant) {
            $variants[] = $this->mapDataToVariant($variant['node']);
        }

        return $variants;
    }

    private function mapDataToVariant(array $variantData): Variant
    {
        return new Variant([
            'id' => $variantData['id'],
            'sku' => !empty($variantData['sku'])
                ? $variantData['sku']
                : $variantData['id'],
            'groupId' => $variantData['product']['id'],
            'isOnStock' => !$variantData['currentlyNotInStock'],
            'price' => $this->mapDataToPriceValue($variantData['priceV2']),
            'currency' => $variantData['priceV2']['currencyCode'],
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => [$variantData['image']['originalSrc']],
            // @TODO Include dangerousInnerVariant base on locale flag
            // 'dangerousInnerVariant' => $variantData,
        ]);
    }

    private function mapDataToPriceValue(array $data): int
    {
        return (int)round($data['amount'] * 100);
    }

    private function mapDataToAttributes(array $variantData): array
    {
        return array_combine(
            array_map(
                function (array $attribute): string {
                    return $attribute['name'];
                },
                $variantData['selectedOptions']
            ),
            array_map(
                function (array $attribute) {
                    return $attribute['value'];
                },
                $variantData['selectedOptions']
            )
        );
    }
}
