<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

class SequentialFieldVisitor implements FieldVisitor
{
    /**
     * @var FieldVisitor[]
     */
    private array $orderedInnerVisitors;

    /**
     * @param FieldVisitor[] $orderedInnerVisitors
     */
    public function __construct(array $orderedInnerVisitors)
    {
        $this->orderedInnerVisitors = $orderedInnerVisitors;
    }

    public function processField(FieldConfiguration $configuration, $value, array $fieldPath)
    {
        foreach ($this->orderedInnerVisitors as $innerVisitor) {
            $value = $innerVisitor->processField($configuration, $value, $fieldPath);
        }
        return $value;
    }
}
