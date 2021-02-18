<?php

namespace Frontastic\Common\ShopifyBundle\Domain\Exception;

class QueryException extends \RuntimeException
{
    public static function createFromErrors(array $errors): self
    {
        return new self($errors[0]['message']);
    }
}
