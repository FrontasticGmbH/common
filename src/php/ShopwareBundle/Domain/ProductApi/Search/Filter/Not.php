<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface;
use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#not
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Not extends Multi
{
    protected function getType(): string
    {
        return 'not';
    }
}
