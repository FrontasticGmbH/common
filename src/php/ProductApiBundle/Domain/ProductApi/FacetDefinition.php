<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class FacetDefinition extends ApiDataObject
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
