<?php

namespace Frontastic\Common\SprykerBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Address as AccountApiBundleAddress;

class Address extends AccountApiBundleAddress
{
    /**
     * @var string|null
     */
    public $company;
}
