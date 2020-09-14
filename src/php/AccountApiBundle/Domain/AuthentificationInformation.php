<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class AuthentificationInformation extends DataObject
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
