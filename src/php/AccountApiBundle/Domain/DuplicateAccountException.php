<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Throwable;

class DuplicateAccountException extends \RuntimeException
{
    public function __construct(string $email, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The account %s does already exist', $email), $code, $previous);
    }
}
