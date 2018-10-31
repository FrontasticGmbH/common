<?php
namespace Frontastic\Common\AccountApiBundle\Domain\AccountApi\Exception;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Exception;

class InvalidQueryException extends Exception
{
    public function __construct(Query $query, $property, $expected, $actual)
    {
        parent::__construct(sprintf(
            'Query property %s::$%s must be of type %s, got %s.',
            get_class($query),
            $property,
            $expected,
            $actual
        ));
    }
}
