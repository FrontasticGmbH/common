<?php

namespace Frontastic\Common\Mvc\Exception;

use Exception;

/**
 * Thrown when accessing information about a user when none is authenticated.
 */
class UnauthenticatedUserException extends Exception
{
}
