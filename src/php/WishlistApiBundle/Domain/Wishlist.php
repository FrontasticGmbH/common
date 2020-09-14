<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * Class Wishlist
 *
 * @package Frontastic\Common\WishlistApiBundle\Domain
 * @type
 */
class Wishlist extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $wishlistId;

    /**
     * @var string
     */
    public $wishlistVersion;

    /**
     * @var string
     */
    public $anonymousId;

    /**
     * @var string
     */
    public $accountId;

    /**
     * @var string[]
     * @required
     */
    public $name = [];

    /**
     * @var \Frontastic\Common\WishlistApiBundle\Domain\LineItem[]
     * @required
     */
    public $lineItems = [];

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
    public $dangerousInnerWishlist;
}
