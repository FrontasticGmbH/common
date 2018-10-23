<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\InvalidQueryException;
use Kore\DataObject\DataObject;

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
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var boolean
     */
    public $loadDangerousInnerData = false;

    /**
     * Optional limit, the default value is <b>25</b>.
     *
     * @var integer
     */
    public $limit = 25;

    /**
     * Optional start offset, default is <b>0</b>.
     *
     * @var integer
     */
    public $offset = 0;

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
        throw new InvalidQueryException($this, $propertyName, $expectedType, gettype($value));
    }
}
