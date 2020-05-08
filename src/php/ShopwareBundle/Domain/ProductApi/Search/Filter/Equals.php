<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#equals
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Equals extends AbstractFilter
{
    protected function assertFilter(): void
    {
        if ($this->value !== null && !is_string($this->value) && !is_numeric($this->value) && !is_bool($this->value)) {
            throw new InvalidArgumentException(
                sprintf('Value must be bool, null, number or string, %s given', gettype($this->value))
            );
        }
    }

    protected function getType(): string
    {
        return 'equals';
    }
}
