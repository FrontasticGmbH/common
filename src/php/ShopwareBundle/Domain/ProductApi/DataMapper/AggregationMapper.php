<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface;

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

    public function map(array $aggregationData)
    {
        $facetsByHandle = $this->getFacetsByHandle($this->getQuery()->facets);

        $result = [];
        foreach ($aggregationData as $aggregationName => $aggregationResult) {
            [$aggregationType, $handle] = explode(self::AGGREGATION_NAME_SEPARATOR, $aggregationName, 2);

            $aggregation = $this->aggregationFactory->createFromType($aggregationType);
            $aggregation->field = $handle;
            $aggregation->setResultData($aggregationResult);

            $queryFacet = $facetsByHandle[$handle] ?? null;

            if ($queryFacet instanceof Query\TermFacet) {
                $resultFacet = $this->mapAggregationToTermFacet($aggregation, $queryFacet);
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
            'min' => $aggregation->getResultData()['min'],
            'max' => $aggregation->getResultData()['max'],
            'value' => [
                'min' => $rangeQueryFacet->min,
                'max' => $rangeQueryFacet->max,
            ],
        ]);
    }

    private function mapAggregationToTermFacet(
        SearchAggregationInterface $aggregation,
        Query\TermFacet $termQueryFacet
    ): Result\TermFacet {
        $selected = count($termQueryFacet->terms) > 0;

        $selectedTermsMap = ($selected) ? array_fill_keys($termQueryFacet->terms, true) : [];

        $terms = [];
        foreach ($aggregation->getResultData() as $item) {
            $value = $this->resolveTranslatedValue($item, 'name');

            $term = new Result\Term([
                'name' => $value,
                'handle' => $value,
                'value' => $item['id'],
                'selected' => isset($selectedTermsMap[$item['id']])
            ]);

            $terms[] = $term;
        }

        return new Result\TermFacet([
            'key' => $termQueryFacet->handle,
            'handle' => $termQueryFacet->handle,
            'selected' => $selected,
            'terms' => $terms,
        ]);
    }
}
