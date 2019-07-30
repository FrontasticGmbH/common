<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @TODO: Refactor result parsing and request generation out of here.
 */
class Commercetools implements ProductApi
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var ProductApi\Commercetools\Options
     */
    private $options;

    /**
     * @var string?
     */
    private $localeOverwrite;

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client
     * @param Mapper $mapper
     * @param string $localeOverwrite
     */
    public function __construct(Client $client, Mapper $mapper, $localeOverwrite = null)
    {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->localeOverwrite = $localeOverwrite;
        $this->options = new ProductApi\Commercetools\Options();
    }

    /**
     * Overwrite default commerecetools options.
     *
     * Explicitely NOT part of the ProductApi interface because Commercetools specific and only to be used during
     * factoring!
     *
     * @param Commercetools\Options $options
     */
    public function setOptions(ProductApi\Commercetools\Options $options): void
    {
        $this->options = $options;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category[]
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function getCategories(CategoryQuery $query): array
    {
        $categories = $this->client
            ->fetchAsync(
                '/categories',
                [
                    'offset' => $query->offset,
                    'limit' => $query->limit,
                ]
            )
            ->wait();

        $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);

        $categoryNameMap = [];
        foreach ($categories as $category) {
            $localizedCategoryName = $category['name'][$locale->language] ?? '';
            $categoryNameMap[$category['id']] = $localizedCategoryName;
        }

        $categoryMap = [];
        foreach ($categories as $category) {
            $path =
                rtrim(
                    '/' .
                    implode(
                        '/',
                        array_map(
                            function (array $ancestor) use ($categoryNameMap) {
                                // If the offset is > 0 we might not have seen the ancestor of this node. Since the
                                // $path is only used to sort the categories we use the ID of the ancestor if we don't
                                // know the path.
                                return $categoryNameMap[$ancestor['id']] ?? $ancestor['id'];
                            },
                            $category['ancestors']
                        )
                    ),
                    '/'
                )
                . '/' . $categoryNameMap[$category['id']];

            $categoryMap[$path] = new Category([
                'categoryId' => $category['id'],
                'name' => $categoryNameMap[$category['id']],
                'depth' => count($category['ancestors']),
                'path' =>
                    rtrim(
                        '/' .
                        implode(
                            '/',
                            array_map(
                                function (array $ancestor) {
                                    return $ancestor['id'];
                                },
                                $category['ancestors']
                            )
                        ),
                        '/'
                    )
                    . '/' . $category['id'],
            ]);
        }

        ksort($categoryMap);
        return array_values($categoryMap);
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        $result = $this->client->fetchAsync('/product-types')->wait();

        return array_map(
            function ($productType) use ($query): ProductType {
                return new ProductType([
                    'productTypeId' => $productType['id'],
                    'name' => $productType['name'],
                    'dangerousInnerProductType' => $this->mapper->dataToDangerousInnerData($productType, $query),
                ]);
            },
            $result->results
        );
    }

    public function getProduct(ProductQuery $query, string $mode = self::QUERY_SYNC): ?object
    {
        if ($query->sku) {
            $promise = $this
                ->query($query, self::QUERY_ASYNC)
                ->then(
                    function (Result $productQueryResult) {
                        $product = reset($productQueryResult->items);
                        return $product === false ? null : $product;
                    }
                );
        } else {
            $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);
            $parameters = ['priceCurrency' => $locale->currency, 'priceCountry' => $locale->territory];

            $promise = $this->client
                ->fetchAsyncById('/products', $query->productId, $parameters)
                ->then(function ($product) use ($query) {
                    return $this->mapper->dataToProduct($product, $query);
                });
        }

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    /**
     * @return Result|PromiseInterface
     */
    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);
        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'filter' => [],
            'filter.query' => $query->filter ?: [],
            'filter.facets' => [],
            'facet' => $this->mapper->facetsToRequest($this->options->facetsToQuery, $locale),
            'priceCurrency' => $locale->currency,
            'priceCountry' => $locale->territory,
        ];

        if ($query->productType) {
            $parameters['filter.query'][] = sprintf('productType.id:"%s"', $query->productType);
        }
        if ($query->category) {
            $parameters['filter.query'][] = sprintf('categories.id: subtree("%s")', $query->category);
        }
        if ($query->query) {
            $parameters[sprintf('text.%s', $locale->language)] = $query->query;
        }
        if ($query->productIds) {
            $parameters['filter.query'][] = sprintf('id: "%s"', join('","', $query->productIds));
        }
        if ($query->sku) {
            $parameters['filter.query'][] = sprintf('variants.sku:"%s"', $query->sku);
        }
        if ($query->skus) {
            $parameters['filter.query'][] = sprintf('variants.sku:"%s"', join('","', $query->skus));
        }
        if ($query->sortAttributes) {
            $parameters['sort'] = array_map(
                function (string $direction, string $field): string {
                    return $field . ($direction === ProductQuery::SORT_ORDER_ASCENDING ? ' asc' : ' desc');
                },
                $query->sortAttributes,
                array_keys($query->sortAttributes)
            );
        }
        $facetsToFilter = $this->mapper->facetsToFilter(
            $query->facets,
            $this->options->facetsToQuery,
            $locale
        );
        $parameters['filter'] = $facetsToFilter;
        $parameters['filter.facets'] = $facetsToFilter;

        $promise = $this->client
            ->fetchAsync('/product-projections/search', array_filter($parameters))
            ->then(function ($result) use ($query) {
                return new Result([
                    'offset' => $result->offset,
                    'total' => $result->total,
                    'count' => $result->count,
                    'items' => array_map(
                        function (array $productData) use ($query) {
                            return $this->mapper->dataToProduct($productData, $query);
                        },
                        $result->results
                    ),
                    'facets' => $this->mapper->dataToFacets($result->facets, $query),
                    'query' => clone $query,
                ]);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
