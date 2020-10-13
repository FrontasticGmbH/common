<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class FacetDefinition extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $attributeType;

    /**
     * @var string
     * @required
     */
    public $attributeId;
}
