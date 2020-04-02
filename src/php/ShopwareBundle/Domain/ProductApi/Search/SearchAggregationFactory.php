<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;
use InvalidArgumentException;

class SearchAggregationFactory
{
    /**
     * @var string[]
     */
    private static $aggregationTypeMap = [
        # Metric
        Aggregation\Avg::TYPE => Aggregation\Avg::class,
        Aggregation\Count::TYPE => Aggregation\Count::class,
        Aggregation\Max::TYPE => Aggregation\Max::class,
        Aggregation\Min::TYPE => Aggregation\Min::class,
        Aggregation\Stats::TYPE => Aggregation\Stats::class,
        Aggregation\Sum::TYPE => Aggregation\Sum::class,

        #Bucket
        Aggregation\Entity::TYPE => Aggregation\Entity::class,
        Aggregation\Filter::TYPE => Aggregation\Filter::class,
        Aggregation\Histogram::TYPE => Aggregation\Histogram::class,
        Aggregation\Terms::TYPE => Aggregation\Terms::class,
    ];

    public function createFromType(
        string $aggregationType,
        array $aggregationConfig,
        array $aggregationResultData = []
    ): SearchAggregationInterface {
        $aggregationClass = self::$aggregationTypeMap[$aggregationType] ?? null;

        if ($aggregationClass === null) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid aggregation type `%s`, available types: `%s`',
                    $aggregationType,
                    implode(', ', array_keys(self::$aggregationTypeMap))
                )
            );
        }

        $aggregation = $this->getAggregationInstance($aggregationClass, $aggregationConfig);

        if (!empty($aggregationResultData)) {
            $aggregation->setResultData($aggregationResultData);
        }

        return $aggregation;
    }

    private function getAggregationInstance(string $class, array $config): SearchAggregationInterface
    {
        return new $class($config);
    }
}
