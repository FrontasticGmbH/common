<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

class InvalidQueryException extends Exception
{
    public static function invalidPropertyType(
        Query $query,
        string $property,
        string $expectedType,
        string $actualType
    ): InvalidQueryException {
        return new static(sprintf(
            'Query property %s::$%s must be of type %s, got %s.',
            get_class($query),
            $property,
            $expectedType,
            $actualType
        ));
    }

    public static function emptyLocale(): InvalidQueryException
    {
        return new static('Query locale must not be empty.');
    }
}
