<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Session extends ApiDataObject
{
    /**
     * Flags a session as stateless
     */
    const STATELESS = 'stateless.api.call';

    /**
     * @var boolean
     * @required
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
