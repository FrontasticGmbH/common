<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

class Address extends DataObject
{
    /**
     * @var string
     */
    public $addressId;

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
    public $postalCode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $country;

    /**
     * @var bool
     */
    public $isDefaultBillingAddress = false;

    /**
     * @var bool
     */
    public $isDefaultShippingAddress = false;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerAddress;
}
