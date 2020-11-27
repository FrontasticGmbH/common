<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class PasswordResetToken extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $email;

    /**
     * @var string|null
     */
    public $confirmationToken;

    /**
     * @var \DateTime|null
     */
    public $tokenValidUntil;
}
