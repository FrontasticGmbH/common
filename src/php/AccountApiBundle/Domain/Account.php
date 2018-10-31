<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

class Account extends DataObject
{
    /**
     * @var string
     */
    public $accountId;

    /**
     * @var string
     */
    public $email;
}
