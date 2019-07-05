<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

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
