<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class AuthentificationInformation extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $email;

    /**
     * @var string
     * @required
     */
    public $password;

    /**
     * @var string
     * @required
     */
    public $newPassword;
}
