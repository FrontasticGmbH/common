<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class AttributeFilter extends DataObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;
}
