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
     */
    public $shippingMethodId;

    /**
     * @var string
     */
    public $name;

    /**
     * @deprecated use `ShippingInfo.price` instead
     * @var integer
     */
    public $price = 0;

    /**
     * Localized description of the shipping method.
     *
     * @var string
     */
    public $description;

    /**
     * @var ?ShippingRate[]
     */
    public $rates;

    /**
     * @var ?mixed
     */
    public $dangerousInnerShippingMethod;
}
