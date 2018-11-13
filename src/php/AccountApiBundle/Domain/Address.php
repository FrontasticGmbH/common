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
}
