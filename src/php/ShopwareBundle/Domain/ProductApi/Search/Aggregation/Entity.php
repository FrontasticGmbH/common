<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

use InvalidArgumentException;

/**
 * @see https://docs.shopware.com/en/shopware-platform-dev-en/api/filter-search-limit#entity-aggregation
 * @example paas/libraries/common/src/php/ShopwareBundle/Resources/examples/aggregations_example.php
 */
class Entity extends AbstractBucketAggregation
{
    public const TYPE = 'entity';

    protected const AGG_RESULT_KEY = 'entities';

    private const AGG_KEY_DEFINITION = 'definition';

    /**
     * Definition name (const ENTITY_NAME) taken from target Shopware entity definition
     *
     * @var string
     */
    public $definition;

    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();

        $result[self::AGG_KEY_DEFINITION] = $this->definition;

        return $result;
    }

    protected function assertAggregation(): void
    {
        parent::assertAggregation();

        if (empty($this->definition)) {
            throw new InvalidArgumentException('Definition can not be empty');
        }
    }

    protected function getType(): string
    {
        return self::TYPE;
    }
}
