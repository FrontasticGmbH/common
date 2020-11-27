<?php


namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

class ShippingLocation extends ApiDataObject
{
    /**
     * 2 letter ISO code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
     *
     * @var string
     */
    public $country;

    /**
     * @var ?string
     */
    public $state;

    /**
     * @var ?mixed
     */
    public $dangerousInnerShippingLocation;
}
