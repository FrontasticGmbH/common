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
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $newPassword;
}
