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
     * @required
     */
    public $name;

    /**
     * @var string
     * @required
     */
    public $value;
}
