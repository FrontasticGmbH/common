<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class FacetDefinition extends DataObject
{
    /** @var string */
    public $attributeType;

    /** @var string */
    public $attributeId;
}
