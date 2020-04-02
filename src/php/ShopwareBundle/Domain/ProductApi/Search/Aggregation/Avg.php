<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#avg-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Avg extends AbstractMetricAggregation
{
    public const TYPE = 'avg';

    protected function getType(): string
    {
        return self::TYPE;
    }
}
