<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Search\Filter;

use Frontastic\Common\ShopwareBundle\Domain\Search\SearchFilterInterface;
use InvalidArgumentException;
use Kore\DataObject\DataObject;

abstract class AbstractFilter extends DataObject implements SearchFilterInterface
{
    public $field;

    public $value;

    abstract public function getType(): string;

    abstract protected function assertFilter(): void;

    public function jsonSerialize(): array
    {
        $this->assertField();
        $this->assertFilter();

        return [
            'type' => $this->getType(),
            'field' => $this->field,
            'value' => $this->value,
        ];
    }

    protected function assertField(): void
    {
        if (empty($this->field)) {
            throw new InvalidArgumentException('Field can not be empty');
        }
    }
}
