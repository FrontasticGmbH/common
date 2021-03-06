<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * Class Product
 *
 * @property-read string $sku
 * @property-read array $attributes
 * @property-read array $images
 * @property-read int $price
 * @property-read string $currency
 * @type
 */
class Product extends ApiDataObject
{
    /**
     * @required
     * @var string
     */
    public $productId;

    /**
     * The date and time when this product was last changed or `null` if the
     * date is unknown.
     *
     * // TODO: Do we want to start typing properties now that we're on 7.4?
     *
     * @var ?\DateTimeImmutable
     */
    public ?\DateTimeImmutable $changed;

    /**
     * @var ?string
     */
    public $version;

    /**
     * @required
     * @var string
     */
    public $name;

    /**
     * @required
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string[]
     */
    public $categories = [];

    /**
     * @required
     * @var \Frontastic\Common\ProductApiBundle\Domain\Variant[]
     */
    public $variants = [];

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * Should only be accessed in lifecycle event listeners,
     * and not in controllers, because ProductApiWithoutInner removes
     * this value before the product is returned to a controller.
     *
     * @var mixed
     */
    public $dangerousInnerProduct;

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'sku':
                return $this->variants[0]->sku;
            case 'attributes':
                return $this->variants[0]->attributes;
            case 'images':
                return $this->variants[0]->images;
            case 'price':
                return $this->variants[0]->price;
            case 'currency':
                return $this->variants[0]->currency;
        }
        return parent::__get($name);
    }
}
