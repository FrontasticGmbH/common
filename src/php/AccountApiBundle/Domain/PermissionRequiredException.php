<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PermissionRequiredException extends AccessDeniedHttpException
{
}
