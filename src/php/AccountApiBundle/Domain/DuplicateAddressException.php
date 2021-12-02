<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Throwable;

class DuplicateAddressException extends \RuntimeException
{
    public function __construct(string $address, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The address "%s" already exist for customer', $address), $code, $previous);
    }
}
