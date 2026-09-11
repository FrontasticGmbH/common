<?php

namespace Frontastic\Common\CoreBundle\Domain;

abstract class Mailer
{
    abstract public function sendToUser(
        $user,
        string $type,
        string $subject,
        array $parameters = array()
    );
}
