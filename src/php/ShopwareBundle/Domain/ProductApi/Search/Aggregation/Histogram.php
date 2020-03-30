<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#histogram-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Histogram extends AbstractBucketAggregation
{
    private const AGG_KEY_INTERVAL = 'interval';

    private const ALLOWED_INTERVALS = [
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'quarter',
        'year',
        'day'
    ];
    public $interval;

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();
        $result[self::AGG_KEY_INTERVAL] = $this->interval;

        return $result;
    }

    protected function assertAggregation(): void
    {
        parent::assertAggregation();

        if (empty($this->interval)) {
            throw new InvalidArgumentException('Interval can not be empty');
        }

        if (in_array($this->interval, self::ALLOWED_INTERVALS, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown interval detected: `%s`. Allowed `%s`',
                    $this->interval,
                    implode(', ', self::ALLOWED_INTERVALS)
                )
            );
        }
    }

    protected function getType(): string
    {
        return 'histogram';
    }
}
