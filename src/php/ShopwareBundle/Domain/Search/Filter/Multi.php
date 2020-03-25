<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\Search\SearchFilterInterface;
use InvalidArgumentException;
use Kore\DataObject\DataObject;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#multi
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Multi extends AbstractFilter
{
    public function getType(): string
    {
        return 'multi';
    }

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        # Special case, value is exchanged with queries
        unset($result['value']);
        $result['queries'] = $this->value;

        if (!empty($this->operator)) {
            $result['operator'] = $this->operator;
        }

        return $result;
    }

    protected function assertField(): void
    {
        #Field is not needed for this type of query
    }

    protected function assertFilter(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('At least one query must be specified');
        }

        if (!is_array($this->value)) {
            throw new InvalidArgumentException(
                sprintf('Value must be array, %s given', gettype($this->value))
            );
        }

        foreach ($this->value as $item) {
            if (!$item instanceof SearchFilterInterface) {
                throw new InvalidArgumentException(
                    sprintf('Query must be instance of SearchFilterInterface, %s given', gettype($this->value))
                );
            }
        }
    }
}
