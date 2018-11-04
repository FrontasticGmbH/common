<?php

namespace Frontastic\Backstage\UserBundle\Domain;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PermissionRequiredException extends AccessDeniedHttpException
{
}
