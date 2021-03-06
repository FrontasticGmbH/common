<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class ProductType extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $productTypeId;

    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerProductType;
}
