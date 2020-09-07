<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\SingleProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiBase;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @TODO: Refactor result parsing and request generation out of here.
 */
class Commercetools extends ProductApiBase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var Commercetools\Locale\CommercetoolsLocaleCreator
     */
    private $localeCreator;

    /**
     * @var EnabledFacetService
     */
    private $enabledFacetService;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(
        Client $client,
        Mapper $mapper,
        Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
        EnabledFacetService $enabledFacetService,
        string $defaultLocale
    ) {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->localeCreator = $localeCreator;
        $this->defaultLocale = $defaultLocale;
        $this->enabledFacetService = $enabledFacetService;
    }

    /**
     * @throws RequestException
     */
    protected function queryCategoriesImplementation(CategoryQuery $query): Result
    {
        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
        ];

        $locale = $this->localeCreator->createLocaleFromString($query->locale);

        if ($query->slug) {
            $parameters['where'] = sprintf('slug(%s="%s")', $locale->language, $query->slug);
        }

        $categories = $this->client
            ->fetchAsync('/categories', $parameters)
            ->wait();

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

            $categoryObject = new Category([
                'categoryId' => $category['id'],
                'name' => $categoryNameMap[$category['id']],
                'slug' => $category['slug'][$locale->language] ?? '',
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

            if ($query->loadDangerousInnerData) {
                $categoryObject->dangerousInnerCategory = $category;
            }

            $categoryMap[$path] = $categoryObject;
        }

        ksort($categoryMap);
        $categoryItems = array_values($categoryMap);

        return new ProductApi\Result([
            'count' => count($categoryItems),
            'items' => $categoryItems,
            'query' => clone($query),
        ]);
    }

    protected function getProductTypesImplementation(ProductTypeQuery $query): array
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

    protected function getProductImplementation(SingleProductQuery $query): PromiseInterface
    {
        if ($query->sku) {
            return $this
                ->query(
                    new ProductQuery([
                        'skus' => [$query->sku],
                        'locale' => $query->locale,
                        'rawApiInput' => $query->rawApiInput,
                        'loadDangerousInnerData' => $query->loadDangerousInnerData,
                    ]),
                    self::QUERY_ASYNC
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

        $locale = $this->localeCreator->createLocaleFromString($query->locale);
        $parameters = ['priceCurrency' => $locale->currency, 'priceCountry' => $locale->country];

        return $this->client
            ->fetchAsyncById(
                '/products',
                $query->productId,
                array_filter(
                    array_merge($query->rawApiInput, $parameters)
                )
            )
            ->then(
                function ($product) use ($query, $locale) {
                    return $this->mapper->dataToProduct($product, $query, $locale);
                },
                function (\Throwable $exception) use ($query) {
                    if ($exception instanceof RequestException && $exception->getCode() === 404) {
                        throw ProductNotFoundException::byProductId($query->productId);
                    }

                    throw $exception;
                }
            );
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);
        $defaultLocale = $this->localeCreator->createLocaleFromString($this->defaultLocale);
        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'filter' => [],
            'filter.query' => [],
            'filter.facets' => [],
            'facet' => $this->mapper->facetsToRequest(
                $this->enabledFacetService->getEnabledFacetDefinitions(),
                $locale
            ),
            'priceCurrency' => $locale->currency,
            'priceCountry' => $locale->country,
            'fuzzy' => $query->fuzzy ? 'true' : 'false',
        ];

        if (count($query->filter) > 0) {
            $parameters['filter.query'] = $this->mapper->prepareQueryFilter($query->filter, $defaultLocale);
        }

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
            $this->enabledFacetService->getEnabledFacetDefinitions(),
            $locale
        );
        $parameters['filter'] = $facetsToFilter;
        $parameters['filter.facets'] = $facetsToFilter;

        return $this->client
            ->fetchAsync(
                '/product-projections/search',
                array_filter(
                    array_merge($query->rawApiInput, $parameters)
                )
            )
            ->then(function ($result) use ($query, $locale) {
                return new Result([
                    'offset' => $result->offset,
                    'total' => $result->total,
                    'count' => $result->count,
                    'items' => array_map(
                        function (array $productData) use ($query, $locale) {
                            return $this->mapper->dataToProduct($productData, $query, $locale);
                        },
                        $result->results
                    ),
                    'facets' => $this->mapper->dataToFacets($result->facets, $query),
                    'query' => clone $query,
                ]);
            });
    }

    public function getDangerousInnerClient(): Client
    {
        return $this->client;
    }
}
