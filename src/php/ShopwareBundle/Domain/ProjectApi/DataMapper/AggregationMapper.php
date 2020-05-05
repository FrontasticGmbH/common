<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationFactory;

class AggregationMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'project_api_aggregation';
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

    public function map($resource)
    {
        $aggregationData = $this->extractAggregations($resource);

        $result = [];
        foreach ($aggregationData as $aggregationName => $aggregationResultData) {
            [$aggregationType, $handle] = explode(self::AGGREGATION_NAME_SEPARATOR, $aggregationName, 2);

            $aggregation = $this->aggregationFactory->createFromType($aggregationType);
            $aggregation->field = $handle;
            $aggregation->name = $aggregationName;
            $aggregation->setResultData($aggregationResultData);

            $result[] = $aggregation;
        }

        return $result;
    }
}
