<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

class Session extends DataObject
{
    /**
     * Flags a session as stateless
     */
    const STATELESS = 'stateless.api.call';

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
