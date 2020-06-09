<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException;
use Kore\DataObject\DataObject;

/**
 * @type
 */
class Query extends DataObject
{
    /**
     * @var string
     */
    public $locale;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those with the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var boolean
     */
    public $loadDangerousInnerData = false;

    /**
     * @param string $propertyName
     * @param string $expectedType
     * @return void
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException
     */
    protected function validateProperty(string $propertyName, string $expectedType): void
    {
        $value = $this->{$propertyName};
        if (is_null($value)) {
            return;
        }
        if (gettype($value) === $expectedType) {
            return;
        }
        throw InvalidQueryException::invalidPropertyType($this, $propertyName, $expectedType, gettype($value));
    }
}
