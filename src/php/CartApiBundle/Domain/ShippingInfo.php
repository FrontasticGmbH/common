<?php

namespace Frontastic\Common\CartApiBundle\Domain;

class ShippingInfo extends ShippingMethod
{
    /**
     * @var integer
     * @required
     */
    public $price = 0;

    /**
     * @var ?mixed
     */
    public $dangerousInnerShippingInfo;
}
