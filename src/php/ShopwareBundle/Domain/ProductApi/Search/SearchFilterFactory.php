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
                $filter = static::buildSearchRangeFilter($queryFilter->handle, $queryFilter->min, $queryFilter->max);
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
                    $filter = static::buildSearchFilterFromTerms($queryFilter->handle, $queryFilter->terms);
                } elseif ($queryFilter instanceof Query\RangeFilter) {
                    $filter = static::buildSearchRangeFilter($queryFilter->handle, $queryFilter->min,
                        $queryFilter->max);
                }

                break;
        }

        if (!$filter instanceof SearchFilterInterface) {
            throw new RuntimeException('Can not create filter');
        }

        return $filter;
    }

    public static function buildSearchRangeFilter(string $field, ?int $min, ?int $max): Filter\Range
    {
        $range = [];

        if ($min > 0) {
            $range[Filter\Range::RANGE_PARAM_GTE] = $min;
        }

        if ($max > 0 && $max !== PHP_INT_MAX) {
            $range[Filter\Range::RANGE_PARAM_LTE] = $max;
        }

        $filter = new Filter\Range();
        $filter->field = $field;
        $filter->value = $range;

        return $filter;
    }

    /**
     * @param string $field
     * @param string[] $terms
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Equals|\Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\EqualsAny
     */
    public static function buildSearchFilterFromTerms(string $field, array $terms)
    {
        $filter = count($terms) === 1
            ? new Filter\Equals()
            : new Filter\EqualsAny();

        $filter->field = $field;
        $filter->value = $terms;

        return $filter;
    }
}
