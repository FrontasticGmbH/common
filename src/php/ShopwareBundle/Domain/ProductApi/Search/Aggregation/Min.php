<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#min-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Min extends AbstractMetricAggregation
{
    public const TYPE = 'min';

    protected function getType(): string
    {
        return self::TYPE;
    }
}
