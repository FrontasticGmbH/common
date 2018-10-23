<?php
namespace Frontastic\Common\ProductApiBundle\Domain;

use Kore\DataObject\DataObject;

class Variant extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var string
     */
    public $groupId;

    /**
     * The product price in cent
     *
     * @var integer
     */
    public $price;

    /**
     * If a discount is applied to the product, this contains the reduced value.
     *
     * @var integer|null
     */
    public $discountedPrice;

    /**
     * A three letter currency code in upper case.
     *
     * @TODO: Currency should only be stored in context. Property should be removed.
     *
     * @var string
     */
    public $currency;

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array
     */
    public $images = [];

    /**
     * @var boolean
     */
    public $isOnStock = true;

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
    public $dangerousInnerVariant;
}
