<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class ShippingMethod extends ApiDataObject
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
