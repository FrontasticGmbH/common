<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
        $categories = $this->client->fetch('/categories', [
            'offset' => $query->offset,
            'limit' => $query->limit
        ]);

        $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);

        $categoryNameMap = [];
        foreach ($categories as $category) {
            $categoryNameMap[$category['id']] = $category['name'][$locale->language];
        }

        $categoryMap = [];
        foreach ($categories as $category) {
            $path = rtrim(
                '/' . implode(
                    '/',
                    array_map(
                        function (array $ancestor) use ($categoryNameMap) {
                            return $categoryNameMap[$ancestor['id']];
                        },
                        $category['ancestors']
                    )
                ),
                '/'
            ) . '/' . $category['name'][$locale->language];

            $categoryMap[$path] = new Category([
                'categoryId' => $category['id'],
                'name' => $category['name'][$locale->language],
                'depth' => count($category['ancestors']),
                'path' => rtrim('/' . implode('/', array_map(
                    function (array $ancestor) {
                        return $ancestor['id'];
                    },
                    $category['ancestors']
                )), '/') . '/' . $category['id']
            ]);
        }

        ksort($categoryMap);
        return array_values($categoryMap);
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        $result = $this->client->fetch('/product-types');

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

    public function getProduct(ProductQuery $query): ?Product
    {
        if ($query->sku) {
            $productQueryResult = $this->query($query);
            return reset($productQueryResult->items);
        }
        return $this->mapper->dataToProduct(
            $this->client->fetchById('/products', $query->productId),
            $query
        );
    }

    public function query(ProductQuery $query): Result
    {
        $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);
        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'filter' => [],
            'filter.query' => [],
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
        if ($query->sortAttributeId) {
            $parameters['sort'] = $query->sortAttributeId;
            if ($query->sortOrder === 'descending') {
                $parameters['sort'] .= ' desc';
            } else {
                $parameters['sort'] .= ' asc';
            }
        }

        $parameters['filter'] = $this->mapper->facetsToFilter(
            $query->facets,
            $this->options->facetsToQuery,
            $locale
        );

        $result = $this->client->fetch('/product-projections/search', array_filter($parameters));

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
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
