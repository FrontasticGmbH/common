<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#terms-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Terms extends AbstractBucketAggregation
{
    public const TYPE = 'terms';

    protected const AGG_RESULT_KEY = 'buckets';

    protected function getType(): string
    {
        return self::TYPE;
    }
}
