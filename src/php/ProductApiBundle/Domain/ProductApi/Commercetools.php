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
     * @todo Hardcoded for now, should come from a dedicated App
     */
    private $facetsToRequest = [
        'variants.attributes.designer',
        'variants.attributes.color.key',
        'variants.attributes.style.key',
        'variants.attributes.gender.key',
        'variants.price.centAmount:range (0 to *)',
    ];

    /**
     * @var string?
     */
    private $localeOverwrite;

    /**
     * CommercetoolsHttpApi constructor.
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client
     */
    public function __construct(Client $client, Mapper $mapper, $localeOverwrite = null)
    {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->localeOverwrite = $localeOverwrite;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category[]
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

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    public function getProduct(ProductQuery $query): ?Product
    {
        return $this->mapper->dataToProduct(
            $this->client->fetchById('/products', $query->productId),
            $query
        );
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result
     */
    public function query(ProductQuery $query): Result
    {
        $locale = Locale::createFromPosix($this->localeOverwrite ?: $query->locale);
        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'filter' => [],
            'filter.query' => [],
            'facet' => $this->facetsToRequest,
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

        $parameters['filter'] = $this->facetsToFilter($query->facets);

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
        ]);
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @param ProductApi\Query\Facet[] $facets
     * @return array
     */
    private function facetsToFilter(array $facets): array
    {
        $filters = [];
        foreach ($facets as $facet) {
            switch (get_class($facet)) {
                case ProductApi\Query\TermFacet::class:
                    /** @var ProductApi\Query\TermFacet $facet */
                    foreach ($facet->terms as $term) {
                        $filters[] = sprintf('%s:"%s"', $facet->handle, $term);
                    }
                    break;

                case ProductApi\Query\RangeFacet::class:
                    /** @var ProductApi\Query\RangeFacet $facet */
                    $filters[] = sprintf('%s:range (%s to %s)', $facet->handle, $facet->min, $facet->max);
                    break;

                default:
                    // @todo Throw error?
                    break;
            }
        }
        return $filters;
    }
}
