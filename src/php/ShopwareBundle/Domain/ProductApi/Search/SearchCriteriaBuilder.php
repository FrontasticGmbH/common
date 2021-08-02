<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Util\HandleParser;

class SearchCriteriaBuilder
{
    private const PRODUCT_SOURCE_FIELDS = [
        'id',
        'productNumber',
        'manufacturerNumber',
        'versionId',
        'name',
        'description',
        'categoryTree',
        'translations',
        'properties.id',
        'properties.groupId',
        'properties.name',
        'properties.group.id',
        'properties.group.name',
        'properties.group.translated.name',
        'properties.translation',
        'tax',
        'stock',
        'children.id',
        'children.parentId',
        'children.versionId',
        'children.productNumber',
        'children.translations',
        'children.media',
        'children.cover',
        'children.price',
        'children.properties.id',
        'children.properties.name',
        'children.properties.translated',
        'children.properties.groupId',
        'children.properties.group.id',
        'children.properties.group.name',
        'children.properties.group.translated.name',
        'available',
        'availableStock',
        'stock',
        'restockTime',
        'price',
        'manufacturer.id',
        'manufacturer.name',
        'manufacturer.translated',
        'media.url',
        'coverId',
        'cover.media.url',
    ];

    private const FIELDS_WITH_NAT_SORTING_ENABLED = [
        'productNumber',
    ];

    /**
     * Holds array of fields for that were already added to aggregation
     *
     * @var string[]
     */
    private static $aggregatedFields = [];

    public static function buildFromCategoryQuery(Query\CategoryQuery $query): array
    {
        return [
            'page' => self::calculatePage($query),
            'limit' => $query->limit,
            'filter' => [
                new Filter\Equals([
                    'field' => 'active',
                    'value' => 1,
                ]),
            ],
        ];
    }

    public static function buildFromProductQuery(Query\ProductQuery $query): array
    {
        $criteria = [
            'page' => self::calculatePage($query),
            'limit' => $query->limit,
            'filter' => [],
            'post-filter' => [],
            'aggregations' => [],
            'associations' => [
                'children' => [
                    'associations' => [
                        'cover' => [],
                        'media' => [],
                        'options' => [
                            'associations' => [
                                'group' => [],
                            ],
                        ],
                        'properties' => [
                            'associations' => [
                                'group' => [],
                            ],
                        ],
                    ],
                ],
                'media' => [],
                'options' => [
                    'associations' => [
                        'group' => [],
                    ],
                ],
                'properties' => [
                    'associations' => [
                        'group' => [],
                    ],
                ],
                'manufacturer' => [],
            ],
            //            'source' => self::PRODUCT_SOURCE_FIELDS,
        ];

        foreach ($query->filter as $filter) {
            self::addFilterToCriteria($criteria, $filter);
        }

        if (!empty($query->query)) {
            $criteria['term'] = $query->query;
        }

        if (!empty($query->productIds)) {
            $criteria['ids'] = $query->productIds;
        } elseif ($query->productId !== null) {
            $criteria['ids'] = [$query->productId];
        }

        if (!empty($query->skus)) {
            $criteria['filter'][] = new Filter\EqualsAny([
                'field' => 'productNumber',
                'value' => $query->skus,
            ]);
        } elseif ($query->sku !== null) {
            $criteria['filter'][] = new Filter\Equals([
                'field' => 'productNumber',
                'value' => $query->sku,
            ]);
        }

        if (!empty($query->category)) {
            $criteria['filter'][] = new Filter\Contains([
                'field' => 'categoryTree',
                'value' => $query->category,
            ]);
        }

        self::$aggregatedFields = [];
        foreach ($query->facets as $facet) {
            self::addAggregationToCriteria($criteria, $facet);
        }

        if ($query->sortAttributes) {
            $criteria['sort'] = array_map(
                static function (string $field, string $direction): array {
                    $result = [
                        'field' => $field,
                        'order' => ($direction === ProductQuery::SORT_ORDER_ASCENDING ? 'asc' : 'desc'),
                    ];

                    if (in_array($field, self::FIELDS_WITH_NAT_SORTING_ENABLED, true)) {
                        $result['naturalSorting'] = true;
                    }

                    return $result;
                },
                array_keys($query->sortAttributes),
                array_values($query->sortAttributes)
            );
        }

        return array_merge($query->rawApiInput, $criteria);
    }

    public static function buildFromSimpleProductQuery(Query\SingleProductQuery $query): array
    {
        $criteria = [
            'page' => 1,
            'limit' => 1,
            'filter' => [],
            // @TODO: uncomment if doing response optimizations
            //            'source' => self::PRODUCT_SOURCE_FIELDS,
        ];

        if ($query->productId !== null) {
            $criteria['filter'][] = new Filter\Equals([
                'field' => 'id',
                'value' => $query->productId,
            ]);
        } elseif ($query->sku !== null) {
            $criteria['filter'][] = new Filter\Equals([
                'field' => 'productNumber',
                'value' => $query->sku,
            ]);
        }

        return $criteria;
    }

    public static function buildFromEmail(string $email): array
    {
        return [
            'filter' => [
                new Filter\Equals([
                    'field' => 'email',
                    'value' => $email,
                ]),
            ],
        ];
    }

    private static function addAggregationToCriteria(array &$criteria, Query\Facet $facet): void
    {
        // Due to the fact that we need to modify the handle, a copy is used instead of original
        // @see paas/libraries/common/src/php/ShopwareBundle/Domain/ProductApi/Search/SearchCriteriaBuilder.php:235
        $facet = clone $facet;

        $aggregation = null;
        $postFilter = null;

        if ($facet instanceof Query\TermFacet) {
            [$field, $definition] = HandleParser::parseFacetHandle($facet->handle);

            $aggregationName = sprintf('%s#%s', $field, $definition);
            $aggregation = new Aggregation\Terms([
                'name' => $aggregationName,
                'field' => $field,
                'aggregation' => new Aggregation\Entity([
                    'name' => $aggregationName . '.inner',
                    'field' => $field,
                    'definition' => $definition,
                ]),
            ]);

            if (!empty($facet->terms)) {
                // Originally $handle does contain more information than just the field, so we
                // explicitly set parsed actual field value here
                $facet->handle = $field;
                $postFilter = SearchFilterFactory::buildSearchFilterFromQueryFacet($facet);
            }
        } elseif ($facet instanceof Query\RangeFacet) {
            $aggregation = new Aggregation\Stats([
                'name' => $facet->handle,
                'field' => $facet->handle,
            ]);

            if ($facet->min !== 0 || $facet->max !== PHP_INT_MAX) {
                $postFilter = SearchFilterFactory::buildSearchRangeFilterFromQueryFacet($facet);
            }
        }

        if ($aggregation !== null && !in_array($aggregation->field, self::$aggregatedFields, true)) {
            $criteria['aggregations'][] = $aggregation;

            self::$aggregatedFields[] = $aggregation->field;
        }

        if ($postFilter !== null) {
            $criteria['post-filter'][] = $postFilter;
        }
    }

    private static function addFilterToCriteria(array &$criteria, Query\Filter $queryFilter): void
    {
        $filter = SearchFilterFactory::createFromQueryFilter($queryFilter);

        if ($filter) {
            $criteria['filter'][] = $filter;
        }
    }

    private static function calculatePage(PaginatedQuery $query): int
    {
        // @TODO: temporary fix for allowed_values problem
        // For products
        if ($query->limit === 24) {
            $query->limit = 25;
        }

        // For categories
        if ($query->limit === 250) {
            $query->limit = 500;
        }

        return (int)ceil($query->offset / $query->limit) + 1;
    }
}
