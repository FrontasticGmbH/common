<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class AttributeFilter extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var string
     * @required
     */
    public $value;
}
