<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;
use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldVisitor;

class NullFieldVisitor implements FieldVisitor
{
    public function processField(FieldConfiguration $configuration, $value)
    {
        return $value;
    }
}
