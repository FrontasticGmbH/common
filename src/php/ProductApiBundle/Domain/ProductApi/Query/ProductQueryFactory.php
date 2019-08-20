<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class ProductQueryFactory
{
    /**
     * @param mixed[string] $defaults Can be overwritten by $parameters
     * @param mixed[string] $parameters Query parameters (typically from HTTP request)
     * @param mixed[string] $overrides Overrides that eventually set fixed values, even if $parameters set these values
     * @return ProductQuery
     */
    public static function queryFromParameters(array $defaults, array $parameters, array $overrides = []): ProductQuery
    {
        $queryParameters = array_merge(
            $defaults,
            $parameters,
            $overrides
        );

        $rawFacets = array_merge(
            $defaults['facets'] ?? [],
            $parameters['facets'] ?? [],
            $overrides['facets'] ?? []
        );

        $rawFilter = array_merge(
            $defaults['filter'] ?? [],
            $parameters['filter'] ?? [],
            $overrides['filter'] ?? []
        );

        $queryParameters['facets'] = [];
        foreach ($rawFacets as $facetHandle => $facetConfig) {
            $queryParameters['facets'][] = self::createFacet($facetHandle, $facetConfig);
        }

        $queryParameters['filter'] = [];
        foreach ($rawFilter as $filterHandle => $filterConfig) {
            $queryParameters['filter'][] = self::createFilter($filterHandle, $filterConfig);
        }

        if (isset($queryParameters['sortAttributeId'])) {
            $queryParameters['sortAttributes'] = [
                $queryParameters['sortAttributeId'] => $queryParameters['sortOrder']
                    ?? ProductQuery::SORT_ORDER_ASCENDING,
            ];

            unset($queryParameters['sortAttributeId']);
            unset($queryParameters['sortOrder']);
        }

        return new ProductQuery($queryParameters);
    }

    /**
     * @param string $facetHandle
     * @param array $facetConfig
     * @return Facet
     */
    private static function createFacet(string $facetHandle, array $facetConfig) : Facet
    {
        $facetConfig['handle'] = $facetHandle;

        switch (true) {
            case (isset($facetConfig['min']) || isset($facetConfig['max'])):
                return new RangeFacet($facetConfig);

            case (isset($facetConfig['terms'])):
                return new TermFacet($facetConfig);

            default:
                throw new \RuntimeException("Unknown facet type for '{$facetHandle}'");
        }
    }

    /**
     * @param string $filterHandle
     * @param array $filterConfig
     * @return Facet
     */
    private static function createFilter(string $filterHandle, array $filterConfig) : Filter
    {
        $filterConfig['handle'] = $filterHandle;

        switch (true) {
            case (isset($filterConfig['min']) || isset($filterConfig['max'])):
                return new RangeFilter($filterConfig);

            case (isset($filterConfig['terms'])):
                return new TermFilter($filterConfig);

            default:
                throw new \RuntimeException("Unknown filter type for '{$filterHandle}'");
        }
    }
}
