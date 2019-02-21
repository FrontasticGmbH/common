<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Symfony\Component\Templating\EngineInterface;

abstract class Mailer
{
    abstract public function sendToUser($user, string $type, string $subject, array $parameters = array());
}
