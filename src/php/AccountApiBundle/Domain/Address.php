<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\BaseObject;

class Address extends BaseObject
{
    /**
     * @var string
     */
    public $addressId;

    /**
     * @var string
     */
    public $salutation;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $streetName;

    /**
     * @var string
     */
    public $streetNumber;

    /**
     * @var string
     */
    public $additionalStreetInfo;

    /**
     * @var string
     */
    public $additionalAddressInfo;

    /**
     * @var string
     */
    public $postalCode;

    /**
     * @var string
     */
    public $city;

    /**
     * 2 letter ISO code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
     *
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var bool
     */
    public $isDefaultBillingAddress = false;

    /**
     * @var bool
     */
    public $isDefaultShippingAddress = false;

    /**
     * Access original object from backend.
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those with the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerAddress;
}
