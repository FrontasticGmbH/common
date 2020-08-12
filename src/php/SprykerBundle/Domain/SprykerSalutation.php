<?php

namespace Frontastic\Common\SprykerBundle\Domain;

use Kore\DataObject\DataObject;

class SprykerSalutation extends DataObject
{
    /**
     * @var string
     */
    public $label = '';

    /**
     * @var string
     */
    public $value = '';

    /**
     * @var bool
     */
    public $default = false;
}
