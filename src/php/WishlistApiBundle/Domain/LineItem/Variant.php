<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\LineItem;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;

/**
 * @type
 */
class Variant extends LineItem
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    public $variant;

    /**
     * @var string
     */
    public $type = 'variant';
}
