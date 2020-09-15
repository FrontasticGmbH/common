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
     * @required
     */
    public $name;

    /**
     * @var integer
     * @required
     */
    public $price = 0;
}
