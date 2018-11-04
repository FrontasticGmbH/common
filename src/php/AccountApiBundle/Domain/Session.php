<?php

namespace Frontastic\Backstage\UserBundle\Domain;

use Kore\DataObject\DataObject;

class Session extends DataObject
{
    /**
     * @var boolean
     */
    public $loggedIn = false;

    /**
     * @var User
     */
    public $user = null;

    /**
     * @var string
     */
    public $message = null;
}
