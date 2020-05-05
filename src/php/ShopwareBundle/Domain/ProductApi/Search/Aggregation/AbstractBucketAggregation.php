<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface;
use InvalidArgumentException;

abstract class AbstractBucketAggregation extends AbstractAggregation
{
    private const AGG_KEY_AGGREGATION = 'aggregation';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface|null
     */
    public $aggregation;

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        if (!empty($this->aggregation)) {
            $result[self::AGG_KEY_AGGREGATION] = $this->aggregation;
        }

        return $result;
    }

    protected function assertAggregation(): void
    {
        if (empty($this->aggregation)) {
            return;
        }

        if (!($this->aggregation instanceof SearchAggregationInterface)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Nested aggregation must be instance of `%s`, `%s` given',
                    SearchAggregationInterface::class,
                    gettype($this->aggregation)
                )
            );
        }
    }
}
