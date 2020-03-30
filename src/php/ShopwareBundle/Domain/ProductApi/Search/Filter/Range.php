<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#range
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Range extends AbstractFilter
{
    private const ALLOWED_PARAMETERS = [
        'gt',
        'gte',
        'lt',
        'lte'
    ];
    private const FILTER_KEY_PARAMETERS = 'parameters';

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        # Special case, value is exchanged with parameters
        unset($result[self::FILTER_KEY_VALUE]);
        $result[self::FILTER_KEY_PARAMETERS] = $this->value;

        return $result;
    }

    protected function assertFilter(): void
    {
        $diff = array_diff_key(array_keys($this->value), self::ALLOWED_PARAMETERS);

        if (empty($diff)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unknown range parameters detected: `%s`. Allowed `%s`',
                    implode(', ', $diff),
                    implode(', ', self::ALLOWED_PARAMETERS)
                )
            );
        }
    }

    protected function getType(): string
    {
        return 'range';
    }
}
