<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;

class SapProductApi implements ProductApi
{
    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var SapDataMapper */
    private $dataMapper;

    public function __construct(SapClient $client, SapLocaleCreator $localeCreator, SapDataMapper $dataMapper)
    {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->dataMapper = $dataMapper;
    }

    public function getCategories(CategoryQuery $query): array
    {
        return $this->client
            ->get(
                '/rest/v2/{siteId}/catalogs/{catalogId}/{catalogVersionId}',
                array_merge(
                    $this->localeCreator->createLocaleFromString($query->locale)->toQueryParameters(),
                    [
                        'fields' => 'FULL',
                    ]
                )
            )
            ->then(function (array $data): array {
                $categories = [];

                foreach ($data['categories'] as $categoryData) {
                    $categories = array_merge(
                        $categories,
                        $this->dataMapper->mapDataToCategories($categoryData)
                    );
                }

                return array_values($categories);
            })
            ->then(function (array $categories) use ($query): array {
                if ($query->slug !== null) {
                    return array_filter(
                        $categories,
                        function (Category $category) use ($query) {
                            return $category->slug === $query->slug;
                        }
                    );
                }
                return $categories;
            })
            ->then(function (array $categories) use ($query): array {
                return array_slice(
                    $categories,
                    $query->offset,
                    $query->limit
                );
            })
            ->wait();
    }

    public function queryCategories(CategoryQuery $query): Result
    {
        $categories = $this->getCategories($query);

        return new Result([
            'count' => count($categories),
            'items' => $categories,
            'query' => clone($query),
        ]);
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        return [
            new ProductType([
                'productTypeId' => 'product',
                'name' => 'Product',
            ]),
        ];
    }

    public function getProduct($originalQuery, string $mode = self::QUERY_SYNC): ?object
    {
        $query = ProductApi\Query\SingleProductQuery::fromLegacyQuery($originalQuery);
        $query->validate();

        if ($query->productId !== null) {
            $code = $query->productId;
        } elseif ($query->sku !== null) {
            // Since we can't access the SKU (or EAN) in the API, we treat the SKU as another code.
            $code = $query->sku;
        } else {
            throw new ProductApi\Exception\InvalidQueryException('Query needs product ID or SKU');
        }

        $promise = $this->client
            ->get(
                '/rest/v2/{siteId}/products/' . $code,
                array_merge(
                    $this->localeCreator->createLocaleFromString($query->locale)->toQueryParameters(),
                    [
                        'fields' => 'FULL',
                    ]
                )
            )
            ->then(function (array $data): Product {
                return $this->dataMapper->mapDataToProduct($data);
            })
            ->otherwise(function (\Throwable $exception) use ($query) {
                if ($exception instanceof SapRequestException && $exception->getCode() === 400) {
                    if ($query->sku !== null) {
                        throw ProductApi\ProductNotFoundException::bySku($query->sku);
                    }
                    throw ProductApi\ProductNotFoundException::byProductId($query->productId);
                }
                throw $exception;
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }
        return $promise;
    }

    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $sapLocale = $this->localeCreator->createLocaleFromString($query->locale);

        $queryFilter = [];

        $codes = [];
        if ($query->sku !== null) {
            $codes[] = $query->sku;
        }
        if ($query->skus !== null) {
            $codes = array_merge($codes, $query->skus);
        }
        if ($query->productId !== null) {
            $codes[] = $query->productId;
        }
        if ($query->productIds !== null) {
            $codes = array_merge($codes, $query->productIds);
        }
        $codes = array_unique($codes);
        if (count($codes) === 1) {
            $queryFilter['code'] = reset($codes);
        } elseif (count($codes) > 1) {
            throw new \InvalidArgumentException('Can currently only search for a single code');
        }

        if ($query->category !== null) {
            $queryFilter['allCategories'] = $query->category;
        }

        $parameters = array_merge(
            $query->rawApiInput,
            $sapLocale->toQueryParameters(),
            [
                'currentPage' => $query->offset / $query->limit,
                'pageSize' => $query->limit,
                'fields' => 'FULL',
                'query' => sprintf(
                    '%s:relevance:%s',
                    $query->query,
                    $this->encodeFilterString($queryFilter)
                ),
            ]
        );

        $promise = $this->client
            ->get('/rest/v2/{siteId}/products/search', $parameters)
            ->then(function (array $result) use ($query): ProductApi\Result {
                $products = array_map([$this->dataMapper, 'mapDataToProduct'], $result['products']);

                return new ProductApi\Result([
                    'offset' => $result['pagination']['currentPage'] * $result['pagination']['pageSize'],
                    'total' => $result['pagination']['totalResults'],
                    'count' => count($products),
                    'items' => $products,
                    'query' => clone $query,
                ]);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }
        return $promise;
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function encodeFilterString(array $filter): string
    {
        $elements = [];

        foreach ($filter as $key => $value) {
            foreach ((array)$value as $item) {
                $elements[] = $key;
                $elements[] = $item;
            }
        }

        return implode(':', $elements);
    }
}
