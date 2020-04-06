<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchFilterInterface;
use InvalidArgumentException;
use Kore\DataObject\DataObject;

abstract class AbstractFilter extends DataObject implements SearchFilterInterface
{
    protected const FILTER_KEY_TYPE = 'type';
    protected const FILTER_KEY_FIELD = 'field';
    protected const FILTER_KEY_VALUE = 'value';

    public $field;

    public $value;

    abstract protected function getType(): string;

    /**
     * @override
     */
    protected function assertFilter(): void
    {
    }

    public function jsonSerialize(): array
    {
        $this->assertField();
        $this->assertFilter();

        return [
            self::FILTER_KEY_TYPE => $this->getType(),
            self::FILTER_KEY_FIELD => $this->field,
            self::FILTER_KEY_VALUE => $this->value,
        ];
    }

    protected function assertField(): void
    {
        if (empty($this->field)) {
            throw new InvalidArgumentException('Field can not be empty');
        }
    }
}
