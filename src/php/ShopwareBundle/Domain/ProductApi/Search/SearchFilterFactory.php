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
                    $filter = static::buildSearchRangeFilter($queryFilter);
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
                    $filter = static::buildSearchFilterFromTerms($queryFilter);
                } elseif ($queryFilter instanceof Query\RangeFilter) {
                    $filter = static::buildSearchRangeFilter($queryFilter);
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

    public static function buildSearchRangeFilter(Query\RangeFilter $queryFilter): Filter\Range
    {
        $range = [];

        if ($queryFilter->min > 0) {
            $range[Filter\Range::RANGE_PARAM_GTE] = $queryFilter->min;
        }

        if ($queryFilter->max > 0 && $queryFilter->max !== PHP_INT_MAX) {
            $range[Filter\Range::RANGE_PARAM_LTE] = $queryFilter->max;
        }

        $searchFilter = new Filter\Range();
        $searchFilter->field = $queryFilter->handle;
        $searchFilter->value = $range;

        return $searchFilter;
    }

    public static function buildSearchFilterFromTerms(Query\TermFilter $queryFilter): SearchFilterInterface
    {
        $searchFilter = count($queryFilter->terms) === 1
            ? new Filter\Equals()
            : new Filter\EqualsAny();

        $searchFilter->field = $queryFilter->handle;
        $searchFilter->value = $queryFilter->terms;

        return $searchFilter;
    }
}
