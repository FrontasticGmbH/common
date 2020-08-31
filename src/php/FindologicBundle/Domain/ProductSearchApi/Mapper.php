<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Filter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class Mapper
{
    /**
     * @return Product[]
     */
    public function dataToProducts(array $items, ProductQuery $query): array
    {
        $locale = Locale::createFromPosix($query->locale);
        $currency = $locale->currency;

        return array_map(
            function ($item) use ($currency, $query) {
                return new Product(
                    [
                        'productId' => $item['id'],
                        'name' => $item['name'],
                        'slug' => $this->getSlugFromUrl($item['url']),
                        'description' => $item['summary'],
                        'categories' => $item['attributes']['cat'],
                        'variants' => empty($item['variants'])
                            ? $this->dataToVariants([$item], $item['id'], $currency)
                            : $this->dataToVariants($item['variants'], $item['id'], $currency),
                        'dangerousInnerProduct' => $query->loadDangerousInnerData ? $item : null,
                    ]
                );
            },
            $items
        );
    }

    /**
     * @param ProductQuery $query
     * @return array
     */
    public function attributesToRequest(ProductQuery $query): array
    {
        $filterAttributes = $this->filtersToRequest($query);
        $facetAttributes = $this->facetsToRequest($query);

        return array_merge_recursive(
            $query->category === null ? [] : [
                'cat' => [
                    $query->category,
                ],
            ],
            $filterAttributes,
            $facetAttributes
        );
    }

    public function sortAttributesToRequest(ProductQuery $query) : ?string
    {
        if (empty($query->sortAttributes)) {
            return null;
        }

        $fields = array_keys($query->sortAttributes);

        return $fields[0] . ' dynamic ' . ($query->sortAttributes[$fields[0]] === ProductQuery::SORT_ORDER_ASCENDING ? 'ASC' : 'DESC');
    }

    private function getSlugFromUrl(string $url): string
    {
        $urlWithoutQueryString = strtok($url, '?');

        preg_match('/[^\/]+(?=\/$|$)/', $urlWithoutQueryString, $matches);

        return $matches[0];
    }

    /**
     * @return Variant[]
     */
    private function dataToVariants(array $variants, string $itemId, string $currency): array
    {
        return array_map(
            function ($variant) use ($itemId, $currency) {
                return new Variant(
                    [
                        'id' => $variant['id'],
                        'sku' => current($variant['ordernumbers']),
                        'groupId' => $itemId,
                        'price' => intval($variant['price'] * 100),
                        'currency' => $currency,
                        'attributes' => $variant['attributes'],
                        'images' => [$variant['imageUrl']],
                        'dangerousInnerVariant' => $variant,
                    ]
                );
            },
            $variants
        );
    }

    private function facetsToRequest(ProductQuery $query): array
    {
        return array_map(
            function (Facet $facet) {
                if ($facet instanceof TermFacet) {
                    return [
                        $facet->handle => $facet->terms,
                    ];
                } else {
                    if ($facet instanceof RangeFacet) {
                        return [
                            $facet->handle => [
                                'min' => $facet->min,
                                'max' => $facet->max,
                            ],
                        ];
                    } else {
                        throw new \RuntimeException('Unsupported facet type ' . get_class($facet));
                    }
                }

            },
            $query->facets
        );
    }

    private function filtersToRequest(ProductQuery $query): array
    {
        return array_map(
            function (Filter $filter) {
                if ($filter instanceof TermFilter) {
                    return [
                        $filter->handle => $filter->terms,
                    ];
                } else {
                    if ($filter instanceof RangeFilter) {
                        return [
                            $filter->handle => [
                                'min' => $filter->min,
                                'max' => $filter->max,
                            ],
                        ];
                    } else {
                        throw new \RuntimeException('Unsupported filter type ' . get_class($filter));
                    }
                }

            },
            $query->filter
        );
    }
}
