<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface;
use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#filter-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Filter extends AbstractBucketAggregation
{
    public const TYPE = 'filter';

    private const AGG_KEY_FILTER = 'filter';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface[]
     */
    public $filters;

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        $result[self::AGG_KEY_FILTER] = $this->filters;
        unset($result[self::AGG_KEY_FIELD]);

        return $result;
    }

    protected function getType(): string
    {
        return self::TYPE;
    }

    protected function assertAggregation(): void
    {
        parent::assertAggregation();

        $this->assertFilters();
    }

    protected function assertField(): void
    {
        #Field is not needed for this type of aggregation
    }

    private function assertFilters(): void
    {
        if (empty($this->filters)) {
            throw new InvalidArgumentException('At least one query must be specified');
        }

        if (!is_array($this->filters)) {
            throw new InvalidArgumentException(
                sprintf('Value must be array, %s given', gettype($this->filters))
            );
        }

        foreach ($this->filters as $filter) {
            if (!$filter instanceof SearchFilterInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Filter must be instance of %s, %s given',
                        SearchFilterInterface::class,
                        gettype($filter)
                    )
                );
            }
        }
    }
}
