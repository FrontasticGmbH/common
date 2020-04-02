<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

class SearchCriteriaBuilder
{
    private const HANDLE_SEPARATOR = '#';
    private const DEFAULT_DEFINITION = 'property_group_option';

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
        'media',
        'cover.media.url',
    ];

    private const FIELDS_WITH_NAT_SORTING_ENABLED = [
        'productNumber',
    ];

    public static function buildFromProductQuery(Query\ProductQuery $query): array
    {
        // @TODO: temp, remove
        if ($query->limit == 24) {
            $query->limit = 25;
        }

        $criteria = [
            'page' => self::calculatePage($query),
            'limit' => $query->limit,
            'filter' => [
                // Exclude variants as they are returned in the list
                new Filter\Equals([
                    'field' => 'parentId',
                    'value' => null
                ])
            ],
            'post-filter' => [],
            'aggregations' => [],
            'associations' => [
                'children' => [
                    'associations' => [
                        'properties' => [
                            'associations' => [
                                'group' => [],
                            ],
                        ],
                        'options' => [
                            'associations' => [
                                'group' => [],
                            ],
                        ],
                    ]
                ],
                'media' => [],
                'options' => [],
//                'categories' => [],
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
        }

        if (!empty($query->skus)) {
            $criteria['filter'][] = new Filter\EqualsAny([
                'field' => 'productNumber',
                'value' => $query->skus,
            ]);
        }

        if (!empty($query->category)) {
            $criteria['filter'][] = new Filter\Contains([
                'field' => 'categoryTree',
                'value' => $query->category,
            ]);
        }

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

        return $criteria;
    }

    public static function buildFromCategoryQuery(Query\CategoryQuery $query): array
    {
        // @TODO: temp, remove
        if ($query->limit == 24) {
            $query->limit = 25;
        }

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

    private static function addAggregationToCriteria(array &$criteria, Query\Facet $facet): void
    {
        $aggregations = [];
        $filter = null;

        if ($facet instanceof Query\TermFacet) {
            [$field, $definition] = array_pad(explode(self::HANDLE_SEPARATOR, $facet->handle), 2, null);

            $aggregations[] = [
                new Aggregation\Terms([
                    'name' => $facet->handle,
                    'field' => $field,
//                    'definition' => $definition ?? self::DEFAULT_DEFINITION,
                    'aggregation' => new Aggregation\Entity([
                        'name' => $facet->handle . '.inner',
                        'field' => 'properties.group',
                        'definition' => self::DEFAULT_DEFINITION,
                    ])
                ])
            ];

//  @TODO: entity aggregation defined above will just provide id => value mapping. In order to achieve
//  id => value (document count) uncomment this aggregation and map its results with entity aggregation
//            $aggregations[] = [
//                new Aggregation\Terms([
//                    'name' => $facet->handle,
//                    'field' => $facet->handle
//                ])
//            ];

            if (!empty($facet->terms)) {
                $filter = SearchFilterFactory::buildSearchFilterFromTerms($field, $facet->terms);
            }
        } elseif ($facet instanceof Query\RangeFacet) {
            $aggregations[] = [
                new Aggregation\Stats([
                    'name' => $facet->handle,
                    'field' => $facet->handle
                ])
            ];

            if ($facet->min !== 0 || $facet->max !== PHP_INT_MAX) {
                $filter = SearchFilterFactory::buildSearchRangeFilter($facet->handle, $facet->min, $facet->max);
            }
        }

        if ($aggregations !== []) {
            $criteria['aggregations'] = array_merge($criteria['aggregations'], ...$aggregations);
        }

        if ($filter !== null) {
            $criteria['post-filter'][] = $filter;
        }
    }

    private static function addFilterToCriteria(array &$criteria, Query\Filter $queryFilter): void
    {
        $criteria['filter'][] = SearchFilterFactory::createFromQueryFilter($queryFilter);
    }

    private static function calculatePage(PaginatedQuery $query): int
    {
        return (int)ceil($query->offset / $query->limit) + 1;
    }
}
