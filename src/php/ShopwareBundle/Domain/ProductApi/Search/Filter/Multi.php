<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface;
use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#multi
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/filters_example.php
 *
 * @property \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface[] $value
 */
class Multi extends AbstractFilter
{
    private const FILTER_KEY_QUERIES = 'queries';
    private const FILTER_KEY_OPERATOR = 'operator';

    public const CONNECTION_AND = 'AND';
    public const CONNECTION_OR = 'OR';
    public const CONNECTION_XOR = 'XOR';

    private const VALID_OPERATORS = [
        'AND', '&&',
        'OR', '||',
        'XOR',
    ];

    /**
     * @var string
     */
    public $operator;

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        # Special case, value is exchanged with queries
        unset($result[self::FILTER_KEY_VALUE]);
        $result[self::FILTER_KEY_QUERIES] = $this->value;

        if (!empty($this->operator)) {
            $result[self::FILTER_KEY_OPERATOR] = $this->operator;
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
                    sprintf('Query must be instance of SearchFilterInterface, %s given', gettype($item))
                );
            }
        }

        $this->assertOperator();
    }

    private function assertOperator(): void
    {
        if (empty($this->operator)) {
            throw new InvalidArgumentException('Operator must be specified');
        }

        if (!in_array($this->operator, self::VALID_OPERATORS, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid operator specified: `%s`. Allowed `%s`',
                    $this->operator,
                    implode(', ', self::VALID_OPERATORS)
                )
            );
        }
    }

    protected function getType(): string
    {
        return 'multi';
    }
}
