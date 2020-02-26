<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Kore\DataObject\DataObject;

class Filter extends DataObject
{
    /**
     * @var string
     */
    public $handle;

    /**
     * @var ?string
     */
    public $attributeType;
}
