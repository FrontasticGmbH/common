<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\Search\SearchFilterInterface;
use Kore\DataObject\DataObject;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#equalsAny
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class EqualsAny extends AbstractFilter
{
    public function getType(): string
    {
        return 'equalsAny';
    }

    protected function assertFilter(): void
    {
        if (!is_array($this->value)) {
            throw new \InvalidArgumentException(
                sprintf('Value must be array, %s given', gettype($this->value))
            );
        }
    }
}
