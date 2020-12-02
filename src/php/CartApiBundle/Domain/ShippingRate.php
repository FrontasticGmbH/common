<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

class ShippingRate extends ApiDataObject
{
    /**
     * Identifier of the shipping zone.
     *
     * @var string
     */
    public $zoneId;

    /**
     * @var string
     */
    public $name;

    /**
     * Shipping locations this rate applies to.
     *
     * @var ?ShippingLocation[]
     */
    public $locations;

    /**
     * 3-letter currency code.
     *
     * @var string
     */
    public $currency;

    /**
     * Price in minor currency (e.g. Cent).
     *
     * @var int
     */
    public $price;

    /**
     * @var ?mixed
     */
    public $dangerousInnerShippingRate;
}
