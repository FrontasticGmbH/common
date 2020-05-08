<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;
use RuntimeException;

class SearchFilterFactory
{
    public static function createFromQueryFilter(Query\Filter $queryFilter): SearchFilterInterface
    {
        $filter = null;

        switch ($queryFilter->attributeType) {
            case 'money':
                if ($queryFilter instanceof Query\RangeFilter) {
                    $filter = static::buildSearchRangeFilterFromQueryFilter($queryFilter);
                }
                break;
            case 'enum':
            case 'localizedEnum':
            case 'localizedText':
            case 'number':
            case 'boolean':
            case 'text':
            case 'reference':
            default:
                if ($queryFilter instanceof Query\TermFilter) {
                    $filter = static::buildSearchFilterFromQueryFilter($queryFilter);
                } elseif ($queryFilter instanceof Query\RangeFilter) {
                    $filter = static::buildSearchRangeFilterFromQueryFilter($queryFilter);
                }

                break;
        }

        if (!$filter instanceof SearchFilterInterface) {
            throw new RuntimeException(
                sprintf(
                    'Can not create search filter for query filter %s with attribute type `%s`',
                    get_class($queryFilter),
                    $queryFilter->attributeType
                )
            );
        }

        return $filter;
    }

    public static function buildSearchRangeFilterFromQueryFacet(Query\RangeFacet $queryFacet): Filter\Range
    {
        return self::buildSearchRangeFilter($queryFacet->handle, $queryFacet->min, $queryFacet->max);
    }

    public static function buildSearchRangeFilterFromQueryFilter(Query\RangeFilter $queryFilter): Filter\Range
    {
        return self::buildSearchRangeFilter($queryFilter->handle, $queryFilter->min, $queryFilter->max);
    }

    public static function buildSearchFilterFromQueryFacet(Query\TermFacet $queryFacet): SearchFilterInterface
    {
        return self::buildSearchFilter($queryFacet->handle, $queryFacet->terms);
    }

    public static function buildSearchFilterFromQueryFilter(Query\TermFilter $queryFilter): SearchFilterInterface
    {
        return self::buildSearchFilter($queryFilter->handle, $queryFilter->terms);
    }

    private static function buildSearchFilter(string $handle, array $terms): SearchFilterInterface
    {
        $searchFilter = count($terms) === 1
            ? new Filter\Equals(['value' => $terms[0]])
            : new Filter\EqualsAny(['value' => $terms]);

        $searchFilter->field = $handle;

        return $searchFilter;
    }

    private static function buildSearchRangeFilter(string $handle, $min, $max): Filter\Range
    {
        $range = [];

        if ($min > 0) {
            $range[Filter\Range::RANGE_PARAM_GTE] = $min;
        }

        if ($max > 0 && $max !== PHP_INT_MAX) {
            $range[Filter\Range::RANGE_PARAM_LTE] = $max;
        }

        $searchFilter = new Filter\Range();
        $searchFilter->field = $handle;
        $searchFilter->value = $range;

        return $searchFilter;
    }
}
