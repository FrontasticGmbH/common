<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Filter extends ApiDataObject
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
