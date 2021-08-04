<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet;
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
    public static function expandQueryEnabledFacets(ProductQuery $query, array $enabledFacetDefinitions): ProductQuery
    {
        $enabledFacetHandles = [];
        $queryFacetsHandles = self::groupQueryFacetsByHandle($query);

        $enabledFacets = [];
        foreach ($enabledFacetDefinitions as $enabledFacetDefinition) {
            // The facet is defined in the query and is among enabled facet definitions
            if (in_array($enabledFacetDefinition->attributeId, $queryFacetsHandles, true)) {
                $enabledFacetHandles[] = $enabledFacetDefinition->attributeId;
                continue;
            }

            $facet = null;
            switch ($enabledFacetDefinition->attributeType) {
                case 'money':
                    $facet = new RangeFacet([
                        'handle' => $enabledFacetDefinition->attributeId
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
                        'handle' => $enabledFacetDefinition->attributeId
                    ]);
                    break;
            }

            if ($facet) {
                $enabledFacetHandles[] = $facet->handle;
                $enabledFacets[] = $facet;
            }
        }

        foreach ($queryFacetsHandles as $index => $queryFacetsHandle) {
            // Remove facet from original query if it's not enabled
            if (!in_array($queryFacetsHandle, $enabledFacetHandles, true)) {
                unset($query->facets[$index]);
            }
        }

        $query->facets = array_values(array_merge($query->facets, $enabledFacets));

        return $query;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet[]
     */
    private static function groupQueryFacetsByHandle(ProductQuery $query): array
    {
        return array_map(static function (Facet $facet) {
            return $facet->handle;
        }, $query->facets);
    }
}
