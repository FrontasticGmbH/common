<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface;
use InvalidArgumentException;
use Kore\DataObject\DataObject;

abstract class AbstractAggregation extends DataObject implements SearchAggregationInterface
{
    protected const AGG_KEY_NAME = 'name';
    protected const AGG_KEY_TYPE = 'type';
    protected const AGG_KEY_FIELD = 'field';

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $name;

    abstract protected function getType(): string;

    /**
     * @override
     */
    protected function assertAggregation(): void
    {

    }

    public function jsonSerialize(): array
    {
        $this->assertAggregation();
        $this->assertField();
        $this->assertName();

        return [
            self::AGG_KEY_NAME => $this->name,
            self::AGG_KEY_TYPE => $this->getType(),
            self::AGG_KEY_FIELD => $this->field,
        ];
    }

    protected function assertName(): void
    {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Name can not be empty');
        }
    }

    protected function assertField(): void
    {
        if (empty($this->field)) {
            throw new InvalidArgumentException('Field can not be empty');
        }
    }
}
