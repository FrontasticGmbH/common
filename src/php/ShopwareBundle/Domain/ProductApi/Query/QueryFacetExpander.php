<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;

class QueryFacetExpander
{
    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\FacetDefinition[] $facetDefinitions
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery
     */
    public static function expandQueryEnabledFacets(ProductQuery $query, array $facetDefinitions): ProductQuery
    {
        foreach ($facetDefinitions as $facetDefinition) {
            switch ($facetDefinition->attributeType) {
                case 'money':
                    $facet = new RangeFacet([
                        'handle' => $facetDefinition->attributeId
                    ]);
                    break;
                case 'number':
                case 'enum':
                case 'localizedText':
                case 'localizedEnum':
                case 'boolean':
                case 'text':
                case 'reference':
                default:
                    $facet = new TermFacet([
                        'handle' => $facetDefinition->attributeId
                    ]);
                    break;
            }

            $query->facets[] = $facet;
        }

        return $query;
    }
}
