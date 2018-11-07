<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

class Session extends DataObject
{
    /**
     * @var boolean
     */
    public $loggedIn = false;

    /**
     * @var Account
     */
    public $account = null;

    /**
     * @var string
     */
    public $message = null;
}
