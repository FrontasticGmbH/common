<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\Search\SearchFilterInterface;
use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#not
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 */
class Not extends AbstractFilter
{
    private const ALLOWED_OPERATORS = [
        'and',
        'or',
    ];

    public $operator;

    public function getType(): string
    {
        return 'not';
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

    protected function assertFilter(): void
    {
        $this->assertValue();
        $this->assertOperator();
    }

    private function assertValue(): void
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

    private function assertOperator(): void
    {
        if (!empty($this->operator) && !in_array($this->operator, self::ALLOWED_OPERATORS, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid operator specified: `%s`. Allowed `%s`',
                    $this->operator,
                    implode(', ', self::ALLOWED_OPERATORS)
                )
            );
        }
    }
}
