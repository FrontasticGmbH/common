<?php

namespace Frontastic\Backstage\UserBundle\Domain;

use Kore\DataObject\DataObject;

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
