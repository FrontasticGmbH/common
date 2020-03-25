<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Search\Filter;

use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#contains
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Contains extends AbstractFilter
{
    public function getType(): string
    {
        return 'contains';
    }

    protected function assertFilter(): void
    {
        if (!is_string($this->value) && !is_numeric($this->value)) {
            throw new InvalidArgumentException(
                sprintf('Value must be string or number, %s given', gettype($this->value))
            );
        }
    }
}
