<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class ShippingMethod extends DataObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $price = 0;
}
