<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Util\HandleParser;

class AggregationMapper extends AbstractDataMapper implements QueryAwareDataMapperInterface
{
    use QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'aggregation';
    private const AGGREGATION_NAME_SEPARATOR = '#';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationFactory
     */
    private $aggregationFactory;

    public function __construct(SearchAggregationFactory $aggregationFactory)
    {
        $this->aggregationFactory = $aggregationFactory;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($aggregationData)
    {
        $aggregationsByHandle = $this->mapDataToAggregations($aggregationData);
        $facetsByHandle = $this->getFacetsByHandle($this->getQuery()->facets);

        $result = [];

        foreach ($facetsByHandle as $handle => $queryFacet) {
            [$field, $fieldDefinition, $propertyGroupId] = HandleParser::parseFacetHandle($handle);

            $aggregationHandle = sprintf('%s#%s', $field, $fieldDefinition);
            $aggregation = $aggregationsByHandle[$aggregationHandle] ??
                $aggregationsByHandle[$field] ??
                null;

            if ($aggregation === null) {
                continue;
            }

            if ($queryFacet instanceof Query\TermFacet) {
                $resultFacet = $this->mapAggregationToTermFacet($aggregation, $queryFacet, $propertyGroupId);
            } elseif ($queryFacet instanceof Query\RangeFacet) {
                $resultFacet = $this->mapAggregationToRangeFacet($aggregation, $queryFacet);
            } else {
                continue;
            }

            $result[] = $resultFacet;
        }

        return $result;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet[] $facets
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet[]
     */
    private function getFacetsByHandle(array $facets): array
    {
        $result = [];

        foreach ($facets as $facet) {
            $result[$facet->handle] = $facet;
        }

        return $result;
    }

    private function mapAggregationToRangeFacet(
        SearchAggregationInterface $aggregation,
        Query\RangeFacet $rangeQueryFacet
    ): Result\RangeFacet {
        $selected = ($rangeQueryFacet->min !== 0 || $rangeQueryFacet->max !== PHP_INT_MAX);

        return new Result\RangeFacet([
            'key' => $rangeQueryFacet->handle,
            'handle' => $rangeQueryFacet->handle,
            'selected' => $selected,
            'min' => $this->convertPriceToCent($aggregation->getResultData()['min']),
            'max' => $this->convertPriceToCent($aggregation->getResultData()['max']),
            'value' => [
                'min' => $rangeQueryFacet->min,
                'max' => $rangeQueryFacet->max,
            ],
        ]);
    }

    private function mapAggregationToTermFacet(
        SearchAggregationInterface $aggregation,
        Query\TermFacet $termQueryFacet,
        ?string $propertyGroupId = null
    ): Result\TermFacet {
        $selected = count($termQueryFacet->terms) > 0;

        $selectedTermsMap = ($selected) ? array_fill_keys($termQueryFacet->terms, true) : [];

        $terms = [];
        foreach ($aggregation->getResultData() as $item) {
            $innerAggregation = array_pop($item);

            if (empty($item['key']) || !isset($innerAggregation['entities'][0])) {
                // Do not add empty values
                // @TODO: perhaps this should be added as well?
                continue;
            }

            $aggregatedPropertyGroupId = $innerAggregation['entities'][0]['groupId'] ?? null;

            if ($propertyGroupId !== null && $aggregatedPropertyGroupId !== $propertyGroupId) {
                // Special case for handling property field aggregation. Every different property group aggregation
                // is executed on same field - property.id. Once the result is received, we must map those properties
                // back to their correct facet which is determined by groupId.
                // This condition skips properties which do not belong to current facet property group
                continue;
            }

            $value = $this->resolveTranslatedValue($innerAggregation['entities'][0], 'name');

            $term = new Result\Term([
                'name' => $value,
                'handle' => $value,
                'value' => $item['key'],
                'count' => $item['count'],
                'selected' => isset($selectedTermsMap[$item['key']])
            ]);

            $terms[] = $term;
        }

        return new Result\TermFacet([
            // @TODO: consider using just the id field to shorten the handle
            'key' => $termQueryFacet->handle,
            'handle' => $termQueryFacet->handle,
            'selected' => $selected,
            'terms' => $terms,
        ]);
    }

    /**
     * @param array $aggregationData
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation\AbstractAggregation[]
     */
    private function mapDataToAggregations(array $aggregationData): array
    {
        $result = [];
        foreach ($aggregationData as $aggregationName => $aggregationResult) {
            list($handle, $aggregationType) = $this->extractAggregationHandleAndType($aggregationName);

            $aggregation = $this->aggregationFactory->createFromType($aggregationType);
            $aggregation->field = $handle;
            $aggregation->setResultData($aggregationResult);

            $result[$handle] = $aggregation;
        }

        return $result;
    }

    private function extractAggregationHandleAndType(string $aggregationName): array
    {
        $handle = $aggregationName;
        $aggregationType = Aggregation\Entity::TYPE;

        if (str_contains($aggregationName, self::AGGREGATION_NAME_SEPARATOR)) {
            [$aggregationType, $handle] = explode(self::AGGREGATION_NAME_SEPARATOR, $aggregationName, 2);
        } elseif ($aggregationName === 'price') {
            // Shopware store-api does not allow to explicitly request this facet. Instead, they will always return
            // price stats on /search and /product-listing/{categoryId} but no in /product API calls.
            $aggregationType = Aggregation\Stats::TYPE;
        }

        return [$handle, $aggregationType];
    }
}
