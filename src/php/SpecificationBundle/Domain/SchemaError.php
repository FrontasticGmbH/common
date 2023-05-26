<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Kore\DataObject\DataObject;

class SchemaError extends DataObject
{
    /**
     * @var ?string
     */
    public $propertyName = null;

    /**
     * @var ?string
     */
    public $errorIndex = null;

    /**
     * @var ?string
     */
    public $errorFlag = null;
}
